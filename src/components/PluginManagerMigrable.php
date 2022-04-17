<?php
/**
 * PluginManagerMigrable.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */

namespace blackcube\core\components;

use blackcube\core\Module;
use yii\console\controllers\MigrateController;
use yii\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * PluginManagerMigrable
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 * @since XXX
 */
abstract class PluginManagerMigrable extends PluginManager {

    /**
     * @var string namespace for plugin migrations
     */
    public $migrationsNamespace;

    /**
     * @var string name of the migration table used
     */
    public $migrationTable = '{{%migration}}';

    /**
     * Returns the file path matching the give namespace.
     * @param string $namespace namespace.
     * @return string file path.
     */
    private function getNamespacePath($namespace)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, Yii::getAlias('@' . str_replace('\\', '/', $namespace)));
    }

    /**
     * @return bool Register plugin
     */
    public function register()
    {
        $migrations = $this->getNewMigrations();
        try {
            ob_start();
            foreach($migrations as $version) {
                $migration = Yii::createObject([
                    'class' => $version,
                    'db' => Module::getInstance()->db,
                ]);
                /* @var $migration Migration */
                if ($migration->up() === false) {
                    throw new \Exception('Migrate error');
                }
                $this->addMigrationHistory($version);
            }
            $content = ob_get_clean();
            $status = $this->registerDbPlugin();
        } catch (\Exception $e) {
            $status = false;
        }
        return $status;
    }

    /**
     * @return bool Unregister plugin
     */
    public function unregister()
    {
        $availableMigrations = $this->findMigrations();
        $appliedMigrations = array_keys($this->getMigrationHistory(null));
        $migrations = array_filter($appliedMigrations, function($item) use ($availableMigrations) {
            return in_array($item, $availableMigrations);
        });
        try {
            ob_start();
            foreach($migrations as $version) {
                $migration = Yii::createObject([
                    'class' => $version,
                    'db' => Module::getInstance()->db,
                ]);
                /* @var $migration Migration */
                if ($migration->down() === false) {
                    throw new \Exception('Migrate error');
                }
                $this->removeMigrationHistory($version);
            }
            $content = ob_get_clean();
            $status = $this->unregisterDbPlugin();
        } catch (\Exception $e) {
            $status = false;
        }

        return $status;
    }

    /**
     * Get all migrations history
     * @return array list of migrations
     */
    protected function getMigrationHistory($limit)
    {
        if ($this->migrationsNamespace === null) {
            return [];
        }

        $db = Module::getInstance()->db;
        $query = (new Query())
            ->select(['version', 'apply_time'])
            ->from($this->migrationTable)
            ->orderBy(['apply_time' => SORT_DESC, 'version' => SORT_DESC]);

        $rows = $query->all($db);

        $history = [];
        foreach ($rows as $key => $row) {
            if ($row['version'] === MigrateController::BASE_MIGRATION) {
                continue;
            }
            if (preg_match('/m?(\d{6}_?\d{6})(\D.*)?$/is', $row['version'], $matches)) {
                $time = str_replace('_', '', $matches[1]);
                $row['canonicalVersion'] = $time;
            } else {
                $row['canonicalVersion'] = $row['version'];
            }
            $row['apply_time'] = (int) $row['apply_time'];
            $history[] = $row;
        }

        usort($history, function ($a, $b) {
            if ($a['apply_time'] === $b['apply_time']) {
                if (($compareResult = strcasecmp($b['canonicalVersion'], $a['canonicalVersion'])) !== 0) {
                    return $compareResult;
                }

                return strcasecmp($b['version'], $a['version']);
            }

            return ($a['apply_time'] > $b['apply_time']) ? -1 : +1;
        });

        $history = array_slice($history, 0, $limit);

        $history = ArrayHelper::map($history, 'version', 'apply_time');

        return $history;
    }

    /**
     * Returns the migrations that are not applied.
     * @return array list of new migrations
     */
    protected function getNewMigrations()
    {
        $applied = [];
        foreach ($this->getMigrationHistory(null) as $class => $time) {
            $applied[trim($class, '\\')] = true;
        }
        $migrations = [];
        $migrationPaths = [];
        $migrationPaths[] = [$this->getNamespacePath($this->migrationsNamespace), $this->migrationsNamespace];

        foreach ($migrationPaths as $item) {
            list($migrationPath, $namespace) = $item;
            if (!file_exists($migrationPath)) {
                continue;
            }
            $handle = opendir($migrationPath);
            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $path = $migrationPath . DIRECTORY_SEPARATOR . $file;
                if (preg_match('/^(m(\d{6}_?\d{6})\D.*?)\.php$/is', $file, $matches) && is_file($path)) {
                    $class = $matches[1];
                    if (!empty($namespace)) {
                        $class = $namespace . '\\' . $class;
                    }
                    $time = str_replace('_', '', $matches[2]);
                    if (!isset($applied[$class])) {
                        $migrations[$time . '\\' . $class] = $class;
                    }
                }
            }
            closedir($handle);
        }
        ksort($migrations);

        return array_values($migrations);
    }

    /**
     * Find the migrations of the plugin
     * @return array
     */
    protected function findMigrations() {
        if ($this->migrationsNamespace === null) {
            return [];
        }
        $migrationPath = $this->getNamespacePath($this->migrationsNamespace);
        $namespace = $this->migrationsNamespace;
        if (file_exists($migrationPath)) {
            $handle = opendir($migrationPath);
            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $path = $migrationPath . DIRECTORY_SEPARATOR . $file;
                if (preg_match('/^(m(\d{6}_?\d{6})\D.*?)\.php$/is', $file, $matches) && is_file($path)) {
                    $class = $namespace . '\\' . $matches[1];
                    $time = str_replace('_', '', $matches[2]);
                    $migrations[$time . '\\' . $class] = $class;
                }
            }
            closedir($handle);
            ksort($migrations);
            return array_values($migrations);
        } else {
            return [];
        }
    }

    /**
     * Update migration history
     */
    protected function addMigrationHistory($version)
    {
        $db = Module::getInstance()->db;
        $command = $db->createCommand();
        $command->insert($this->migrationTable, [
            'version' => $version,
            'apply_time' => time(),
        ])->execute();
    }

    /**
     * Update migration history
     */
    protected function removeMigrationHistory($version)
    {
        $db = Module::getInstance()->db;
        $command = $db->createCommand();
        $command->delete($this->migrationTable, [
            'version' => $version,
        ])->execute();
    }

}
