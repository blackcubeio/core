<?php
/**
 * InitController.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

namespace blackcube\core\commands;

use blackcube\core\models\Node;
use blackcube\core\Module;
use yii\console\Controller;
use yii\console\ExitCode;
use ReflectionClass;
use Yii;

/**
 * Blackcube Core database initialisation
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /
class InitController extends Controller
{

    /**
     * Init database with one node
     * @return int
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $this->stdout(Module::t('console', 'Init CMS Data'."\n"));
        $rootNode = Node::find()->orderBy(['left' => SORT_ASC])->limit(1)->one();
        if ($rootNode === null) {
            $node = Yii::createObject(Node::class);
            $node->setNodePath('1');
            $node->name = 'Root';
            $node->active = true;
            // TODO: ask language
            $node->languageId = 'fr';
            $node->save();
        } else {
            $this->stdout(Module::t('console', 'Root node already exists'."\n"));
        }

        return ExitCode::OK;
    }
}
