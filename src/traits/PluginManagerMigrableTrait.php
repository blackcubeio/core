<?php
/**
 * PluginManagerMigrableTrait.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\traits;

use blackcube\core\models\Plugin;
use blackcube\core\Module;
use Yii;
use yii\base\BootstrapInterface;
use yii\console\controllers\MigrateController;
use yii\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * PluginManagerMigrable trait
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 *
 */
trait PluginManagerMigrableTrait
{
    /**
     * @var string namespace for plugin migrations
     */
    public $migrationsNamespace;

    /**
     * @var string name of the migration table used
     */
    public $migrationTable = '{{%migration}}';

    /**
     * @var Plugin current plugin id db
     */
    protected $dbPlugin;

    /**
     * {@inheritDoc }
     */
    abstract public function getId();

    /**
     * {@inheritDoc }
     */
    abstract public function getName();

    /**
     * {@inheritDoc }
     */
    abstract public function getVersion();

    /**
     * @return Plugin false if plugin is not registered in database
     */
    protected function getDbPlugin()
    {
        if ($this->dbPlugin === null) {
            $plugin = Plugin::find()->andWhere(['id' => $this->getId()])->one();
            if ($plugin instanceof Plugin) {
                $this->dbPlugin = $plugin;
            } else {
                $this->dbPlugin = false;
            }
        }
        return $this->dbPlugin;
    }

    /**
     * {@inheritDoc }
     */
    public function getIsActive() :bool
    {
        $plugin = $this->getDbPlugin();
        if ($plugin !== false) {
            return (bool)$plugin->active;
        }
        return false;
    }

    /**
     * {@inheritDoc }
     */
    public function getIsRegistered() :bool
    {
        $plugin = $this->getDbPlugin();
        if ($plugin !== false) {
            return (bool)$plugin->registered;
        }
        return false;
    }

    /**
     * {@inheritDoc }
     */
    public function activate() :bool
    {
        $plugin = $this->getDbPlugin();
        if ($plugin !== false) {
            $plugin->active = true;
            return $plugin->save(true, ['active', 'dateUpdate']);
        }
        return false;
    }

    /**
     * {@inheritDoc }
     */
    public function deactivate() :bool
    {
        $plugin = $this->getDbPlugin();
        if ($plugin !== false) {
            $plugin->active = false;
            return $plugin->save(true, ['active', 'dateUpdate']);
        }
        return false;
    }

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
     * Helper function to register plugin id DB
     * @return bool
     */
    protected function registerDbPlugin() :bool
    {
        if ($this->getIsRegistered() === false) {
            $plugin = $this->getDbPlugin();
            if ($plugin !== false) {
                $plugin->registered = true;
                return $plugin->save(true, ['registered', 'dateUpdate']);
            }
            return false;
        }
        return false;
    }

    /**
     * Helper function to unregister plugin id DB
     * @return bool
     */
    protected function unregisterDbPlugin() :bool
    {
        if ($this->getIsRegistered() === true) {
            $plugin = $this->getDbPlugin();
            if ($plugin !== false) {
                $plugin->registered = false;
                $plugin->active = false;
                return $plugin->save(true, ['registered', 'active', 'dateUpdate']);
            }
            return false;
        }
        return false;
    }

    /**
     * @return bool Register plugin
     */
    public function register() :bool
    {
        if ($this->getIsRegistered() === false) {
            $migrations = $this->getNewMigrations();
            $transaction = Module::getInstance()->get('db')->beginTransaction();
            try {
                ob_start();
                foreach ($migrations as $version) {
                    $migration = Yii::createObject([
                        'class' => $version,
                        'db' => Module::getInstance()->get('db'),
                    ]);
                    /* @var $migration Migration */
                    if ($migration->up() === false) {
                        throw new \Exception('Migrate error');
                    }
                    $this->addMigrationHistory($version);
                }
                $content = ob_get_clean();
                $status = $this->registerDbPlugin();
                if ($status === true) {
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $status = false;
            }
            return $status;
        }
        return false;
    }

    /**
     * @return bool Unregister plugin
     */
    public function unregister() :bool
    {
        if ($this->getIsRegistered() === true) {
            $transaction = Module::getInstance()->get('db')->beginTransaction();
            $availableMigrations = $this->findMigrations();
            $appliedMigrations = array_keys($this->getMigrationHistory(null));
            $migrations = array_filter($appliedMigrations, function ($item) use ($availableMigrations) {
                return in_array($item, $availableMigrations);
            });
            try {
                ob_start();
                foreach ($migrations as $version) {
                    $migration = Yii::createObject([
                        'class' => $version,
                        'db' => Module::getInstance()->get('db'),
                    ]);
                    /* @var $migration Migration */
                    if ($migration->down() === false) {
                        throw new \Exception('Migrate error');
                    }
                    $this->removeMigrationHistory($version);
                }
                $content = ob_get_clean();
                $status = $this->unregisterDbPlugin();
                if ($status === true) {
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $status = false;
            }

            return $status;
        }
        return false;
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

        $db = Module::getInstance()->get('db');
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
            $migrations = [];
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
        $db = Module::getInstance()->get('db');
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
        $db = Module::getInstance()->get('db');
        $command = $db->createCommand();
        $command->delete($this->migrationTable, [
            'version' => $version,
        ])->execute();
    }

}
