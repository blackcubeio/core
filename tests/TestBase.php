<?php
/**
 * TestBase.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Philippe Gaultier
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package tests\unit
 */
namespace tests;

use blackcube\core\models\Category;
use blackcube\core\models\CategoryBloc;
use blackcube\core\models\Composite;
use blackcube\core\models\CompositeBloc;
use blackcube\core\models\CompositeTag;
use blackcube\core\models\NodeBloc;
use blackcube\core\models\NodeComposite;
use blackcube\core\models\NodeTag;
use blackcube\core\models\Parameter;
use blackcube\core\models\Seo;
use blackcube\core\models\Sitemap;
use blackcube\core\models\Slug;
use blackcube\core\models\BlocType;
use blackcube\core\models\Tag;
use blackcube\core\models\TagBloc;
use blackcube\core\models\Type;
use blackcube\core\models\TypeBlocType;
use blackcube\core\models\Node;
use blackcube\core\models\Bloc;
use Yii;

/**
 * Test base
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Philippe Gaultier
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package tests\unit
 * @since XXX
 */
class TestBase
{

    protected $jsonSchemaBasic = <<<'JSON'
{
    "type": "object",
    "properties": {
        "email": {
            "type": "string",
            "minLength": 2,
            "format": "email"
        },
        "telephone": {
            "type": "string",
            "pattern": "^[0-9]{2}[-.]?[0-9]{2}[-.]?[0-9]{2}[-.]?[0-9]{2}[-.]?[0-9]{2}$",
            "title": "Téléphone",
            "description": "Numéro de téléphone au format XX.XX.XX.XX.XX"
        }
    },
    "required":["email"]
}
JSON;

    protected $parameterList = [
        ['domain' => 'SITE', 'name' => 'NAME', 'value' => 'Site Name'],
        ['domain' => 'SITE', 'name' => 'PAGINATE', 'value' => '10'],
        ['domain' => 'SEARCH', 'name' => 'GOOGLE', 'value' => 'https://google.com/value'],
        ['domain' => 'SEARCH', 'name' => 'BING', 'value' => 'https://bing.com/value'],
    ];

    protected $slugList = [
        1 => ['host' => null, 'path' => 'home', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        2 => ['host' => null, 'path' => 'node', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        3 => ['host' => null, 'path' => 'tag', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        4 => ['host' => 'www.basehost.com', 'path' => 'home-base', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        5 => ['host' => null, 'path' => 'composite', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        6 => ['host' => null, 'path' => 'composite-disabled', 'targetUrl' => null, 'httpCode' => null, 'active' => false],
        7 => ['host' => null, 'path' => 'redirect-google', 'targetUrl' => 'https://www.google.com', 'httpCode' => 301, 'active' => true],
        8 => ['host' => null, 'path' => 'node-1.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        9 => ['host' => null, 'path' => 'node-1.1.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        10 => ['host' => null, 'path' => 'node-1.2.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        11 => ['host' => null, 'path' => 'node-1.2.1.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        12 => ['host' => null, 'path' => 'node-1.2.2.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        13 => ['host' => null, 'path' => 'node-1.3.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        14 => ['host' => null, 'path' => 'node-1.3.1.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        15 => ['host' => null, 'path' => 'node-1.3.2.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        16 => ['host' => null, 'path' => 'no-element.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        17 => ['host' => null, 'path' => 'tag-1.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        18 => ['host' => null, 'path' => 'category-1.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        19 => ['host' => null, 'path' => 'category-test.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        20 => ['host' => null, 'path' => 'tag-test.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        21 => ['host' => null, 'path' => 'composite-test.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],
        22 => ['host' => null, 'path' => 'composite-test-2.html', 'targetUrl' => null, 'httpCode' => null, 'active' => true],

    ];

    protected $sitemapList = [
        1 => ['slugId' => 1, 'frequency' => 'daily', 'priority' => 0.4, 'active' => true],
        2 => ['slugId' => 2, 'frequency' => 'monthly', 'priority' => 0.6, 'active' => true],
        3 => ['slugId' => 3, 'frequency' => 'daily', 'priority' => 0.4, 'active' => false],
        4 => ['slugId' => 4, 'frequency' => 'daily', 'priority' => 0.5, 'active' => true],
    ];

    protected $seoList = [
        1 => ['slugId' => 1, 'canonicalSlugId' => null, 'title' => 'home title', 'description' => 'home description', 'active' => true],
        2 => ['slugId' => 2, 'canonicalSlugId' => null, 'title' => 'node title', 'description' => 'node description', 'active' => true],
        3 => ['slugId' => 3, 'canonicalSlugId' => null, 'title' => 'tag title', 'description' => 'tag description', 'active' => true],
        4 => ['slugId' => 4, 'canonicalSlugId' => null, 'title' => 'base host title', 'description' => 'base host description', 'active' => true],
    ];

    protected $typeList = [
        1 =>  ['name' => 'home', 'route' => 'home', 'minBlocs' => null, 'maxBlocs' => null ],
        2 =>  ['name' => 'home-landing', 'route' => 'home', 'landing', 'minBlocs' => null, 'maxBlocs' => null ],
        3 =>  ['name' => 'subhome', 'route' => 'sub-home', 'minBlocs' => 1, 'maxBlocs' => 4 ],
        4 =>  ['name' => 'taglist', 'route' => 'tag', 'minBlocs' => null, 'maxBlocs' => 3 ],
        5 =>  ['name' => 'demo-type', 'route' => 'demo', 'minBlocs' => null, 'maxBlocs' => 3 ],
        6 =>  ['name' => 'composite-test', 'route' => 'composite', 'minBlocs' => null, 'maxBlocs' => 3 ],
    ];

    protected $blocTypeList = [
        1 =>  ['name' => 'contact', 'template' => <<<'JSON'
{
    "type": "object",
    "properties": {
        "email": {
            "type": "string",
            "minLength": 2,
            "format": "email"
        },
        "telephone": {
            "type": "string",
            "pattern": "^[0-9]{2}[-.]?[0-9]{2}[-.]?[0-9]{2}[-.]?[0-9]{2}[-.]?[0-9]{2}$",
            "title": "Téléphone",
            "description": "Numéro de téléphone au format XX.XX.XX.XX.XX"
        }
    },
    "required":["email"]
}
JSON
        ],
        2 =>  ['name' => 'text', 'template' =>  <<<'JSON'
{
    "type": "object",
    "properties": {
        "title": {
            "type": "string"
        },
        "text": {
            "type": "string",
            "description": "Texte de bloc",
            "minLength": 2
        }
    },
    "required":["text"]
}
JSON
        ],
    ];

    protected $typeBlocTypeList = [
        ['typeId' => 1, 'blocTypeId' => 1, 'allowed' => false],
        ['typeId' => 1, 'blocTypeId' => 2, 'allowed' => true],
        ['typeId' => 2, 'blocTypeId' => 1, 'allowed' => true],
        ['typeId' => 2, 'blocTypeId' => 2, 'allowed' => true],
        ['typeId' => 3, 'blocTypeId' => 1, 'allowed' => true],
        ['typeId' => 3, 'blocTypeId' => 2, 'allowed' => true],
        ['typeId' => 4, 'blocTypeId' => 1, 'allowed' => false],
        ['typeId' => 4, 'blocTypeId' => 2, 'allowed' => true],
    ];

    protected $nodeList = [
        1 => ['nodePath' => '1', 'name' => 'Node '.'1', 'languageId' => 'fr', 'slugId' => 8],
        2 => ['nodePath' => '1.1', 'name' => 'Node '.'1.1', 'languageId' => 'fr', 'slugId' => 9],
        3 => ['nodePath' => '1.2', 'name' => 'Node '.'1.2', 'languageId' => 'fr', 'slugId' => 10],
        4 => ['nodePath' => '1.2.1', 'name' => 'Node '.'1.2.1', 'languageId' => 'fr', 'slugId' => 11],
        5 => ['nodePath' => '1.2.2', 'name' => 'Node '.'1.2.2', 'languageId' => 'fr-FR', 'slugId' => 12],
        6 => ['nodePath' => '1.3', 'name' => 'Node '.'1.3', 'languageId' => 'fr', 'slugId' => 13],
        7 => ['nodePath' => '1.3.1', 'name' => 'Node '.'1.3.1', 'languageId' => 'fr', 'slugId' => 14],
        8 => ['nodePath' => '1.3.2', 'name' => 'Node '.'1.3.2', 'languageId' => 'fr', 'slugId' => 15],
        9 => ['nodePath' => '1.4', 'name' => 'Node '.'1.4', 'languageId' => 'fr'],
        10 => ['nodePath' => '1.4.1', 'name' => 'Node '.'1.4.1', 'languageId' => 'fr'],
        11 => ['nodePath' => '1.4.1.1', 'name' => 'Node '.'1.4.1.1', 'languageId' => 'fr'],
        12 => ['nodePath' => '1.4.1.2', 'name' => 'Node '.'1.4.1.2', 'languageId' => 'fr'],
        13 => ['nodePath' => '1.4.2', 'name' => 'Node '.'1.4.2', 'languageId' => 'fr'],
    ];

    protected $compositeList = [
        1 => ['name' => 'composite 1', 'languageId' => 'fr', 'typeId' => 6, 'slugId' => 5, 'active' => true],
        2 => ['name' => 'composite 2', 'languageId' => 'fr', 'typeId' => 6, 'slugId' => 6, 'active' => true],
        3 => ['name' => 'composite 3', 'languageId' => 'fr', 'typeId' => 6, 'slugId' => 21, 'active' => false],
        4 => ['name' => 'composite 4', 'languageId' => 'fr', 'typeId' => 6, 'active' => true],
        5 => ['name' => 'composite 5', 'languageId' => 'fr', 'typeId' => 6, 'active' => true],
    ];

    protected $categoryList = [
        1 => ['name' => 'Categorie 1', 'languageId' => 'fr', 'slugId' => 18, 'active' => true],
        2 => ['name' => 'Categorie 2', 'languageId' => 'fr', 'active' => true],
        3 => ['name' => 'Categorie 3', 'languageId' => 'fr', 'active' => true],
        4 => ['name' => 'Categorie 4', 'languageId' => 'fr', 'active' => true],
        5 => ['name' => 'Categorie 5', 'languageId' => 'fr-FR', 'active' => true],
        6 => ['name' => 'Categorie 6', 'languageId' => 'fr', 'active' => true],
        7 => ['name' => 'Categorie 7', 'languageId' => 'fr', 'active' => false],
    ];

    protected $tagList = [
        1 => ['name' => 'Categorie 1 - Tag 1', 'categoryId' => 1, 'languageId' => 'fr', 'slugId' => 17, 'active' => true],
        2 => ['name' => 'Categorie 1 - Tag 2', 'categoryId' => 1, 'languageId' => 'fr', 'active' => true],
        3 => ['name' => 'Categorie 1 - Tag 3', 'categoryId' => 1, 'languageId' => 'fr', 'active' => true],
        4 => ['name' => 'Categorie 2 - Tag 1', 'categoryId' => 2, 'languageId' => 'fr', 'active' => true],
        5 => ['name' => 'Categorie 2 - Tag 2', 'categoryId' => 2, 'languageId' => 'fr-FR', 'active' => true],
        6 => ['name' => 'Categorie 2 - Tag 3', 'categoryId' => 2, 'languageId' => 'fr', 'active' => true],
        7 => ['name' => 'Categorie 3 - Tag 1', 'categoryId' => 3, 'languageId' => 'fr', 'active' => true],
        8 => ['name' => 'Categorie 3 - Tag 2', 'categoryId' => 3, 'languageId' => 'fr', 'active' => true],
        9 => ['name' => 'Categorie 3 - Tag 3', 'categoryId' => 3, 'languageId' => 'fr', 'active' => true],
        10 => ['name' => 'Categorie 4 - Tag 1', 'categoryId' => 4, 'languageId' => 'fr', 'active' => true],
        11 => ['name' => 'Categorie 4 - Tag 2', 'categoryId' => 4, 'languageId' => 'fr', 'active' => true],
        12 => ['name' => 'Categorie 4 - Tag 3', 'categoryId' => 4, 'languageId' => 'fr', 'active' => true],
        13 => ['name' => 'Categorie 6 - Tag 1', 'categoryId' => 6, 'languageId' => 'fr', 'active' => false],
        14 => ['name' => 'Categorie 7 - Tag 1', 'categoryId' => 7, 'languageId' => 'fr', 'active' => true],
    ];

    protected $nodeTagLinks = [
        ['nodeId' => 1, 'tagId' => 1], // cat 1
        ['nodeId' => 1, 'tagId' => 2], // cat 1
        ['nodeId' => 1, 'tagId' => 4], // cat 2
        ['nodeId' => 1, 'tagId' => 14], // cat 7
        ['nodeId' => 2, 'tagId' => 8], // cat 3
        ['nodeId' => 3, 'tagId' => 11], // cat 4
        ['nodeId' => 8, 'tagId' => 1], // cat 1
        ['nodeId' => 8, 'tagId' => 4], // cat 2
        ['nodeId' => 8, 'tagId' => 7], // cat 3
        ['nodeId' => 8, 'tagId' => 10], // cat 4
        ['nodeId' => 5, 'tagId' => 13], // cat 6
        ['nodeId' => 5, 'tagId' => 14], // cat 7
    ];

    protected $compositeTagLinks = [
        ['compositeId' => 1, 'tagId' => 1], // cat 1
        ['compositeId' => 1, 'tagId' => 2], // cat 1
        ['compositeId' => 1, 'tagId' => 4], // cat 2
        ['compositeId' => 1, 'tagId' => 13], // cat 6
        ['compositeId' => 1, 'tagId' => 14], // cat 7
        ['compositeId' => 2, 'tagId' => 8], // cat 3
    ];

    protected $nodeCompositeLinks = [
        ['nodeId' => 1, 'compositeId' => 1, 'order' => 1],
        ['nodeId' => 1, 'compositeId' => 2, 'order' => 2],
        ['nodeId' => 3, 'compositeId' => 3, 'order' => 1],
    ];

    protected $blocList = [
        1 => ['blocTypeId' => 2, 'data' => '{"text":"bloc 1"}'],
        2 => ['blocTypeId' => 2, 'data' => '{"text":"bloc 2"}'],
        3 => ['blocTypeId' => 2, 'data' => '{"text":"bloc 3"}'],
        4 => ['blocTypeId' => 2, 'data' => '{"text":"bloc 4"}'],
        5 => ['blocTypeId' => 2, 'data' => '{"text":"bloc 5"}'],
        6 => ['blocTypeId' => 2, 'data' => '{"text":"node 1 - bloc 6"}'],
        7 => ['blocTypeId' => 2, 'data' => '{"text":"node 1 - bloc 7"}'],
        8 => ['blocTypeId' => 2, 'data' => '{"text":"composite 1 - bloc 8"}'],
        9 => ['blocTypeId' => 2, 'data' => '{"text":"composite 1 - bloc 9"}'],
        10 => ['blocTypeId' => 2, 'data' => '{"text":"composite 1 - bloc 10"}'],
        11 => ['blocTypeId' => 2, 'data' => '{"text":"composite 2 - bloc 11"}'],
        12 => ['blocTypeId' => 2, 'data' => '{"text":"composite 3 - bloc 12"}'],
        13 => ['blocTypeId' => 2, 'data' => '{"text":"composite 3 - bloc 13"}'],
        14 => ['blocTypeId' => 2, 'data' => '{"text":"composite 4 - bloc 14"}'],
        15 => ['blocTypeId' => 2, 'data' => '{"text":"composite 4 - bloc 15"}'],
        16 => ['blocTypeId' => 2, 'data' => '{"text":"tag 1 - bloc 16"}'],
        17 => ['blocTypeId' => 2, 'data' => '{"text":"category 1 - bloc 17"}'],
        18 => ['blocTypeId' => 2, 'data' => '{"text":"test bloc"}'],
    ];

    protected $compositeBlocLinks = [
        ['compositeId' => 1, 'blocId' => 8, 'order' => 1],
        ['compositeId' => 1, 'blocId' => 9, 'order' => 2],
        ['compositeId' => 1, 'blocId' => 10, 'order' => 3],
        ['compositeId' => 2, 'blocId' => 11, 'order' => 1],
        ['compositeId' => 3, 'blocId' => 12, 'order' => 1],
        ['compositeId' => 3, 'blocId' => 13, 'order' => 2],
        ['compositeId' => 4, 'blocId' => 14, 'order' => 1],
        ['compositeId' => 4, 'blocId' => 15, 'order' => 2],
    ];

    protected $nodeBlocLinks = [
        ['nodeId' => 1, 'blocId' => 6, 'order' => 1],
        ['nodeId' => 1, 'blocId' => 7, 'order' => 2],
    ];

    protected $categoryBlocLinks = [
        ['categoryId' => 1, 'blocId' => 17, 'order' => 1],
    ];

    protected $tagBlocLinks = [
        ['tagId' => 1, 'blocId' => 16, 'order' => 1],
    ];

    public function _before()
    {
        Parameter::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(Parameter::tableName());

        Sitemap::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(Sitemap::tableName())->execute();

        Node::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(Node::tableName())->execute();

        Composite::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(Composite::tableName())->execute();

        Category::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(Category::tableName())->execute();

        Tag::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(Tag::tableName())->execute();

        Slug::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(Slug::tableName())->execute();

        TypeBlocType::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(TypeBlocType::tableName())->execute();

        Type::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(Type::tableName())->execute();

        BlocType::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(BlocType::tableName())->execute();

        Bloc::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(Bloc::tableName())->execute();

        Seo::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(Seo::tableName())->execute();

        CompositeBloc::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(CompositeBloc::tableName())->execute();

        NodeBloc::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(NodeBloc::tableName())->execute();

        CategoryBloc::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(CategoryBloc::tableName())->execute();

        TagBloc::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(TagBloc::tableName())->execute();

        NodeComposite::deleteAll();
        // Yii::$app->db->createCommand()->resetSequence(NodeComposite::tableName())->execute();

        foreach($this->parameterList as $config) {
            $parameter = new Parameter();
            $parameter->attributes = $config;
            $parameter->save();
        }

        foreach($this->slugList as $id => $config) {
            $slug = new Slug();
            $slug->attributes = $config;
            $slug->id = $id;
            $slug->save();
        }

        foreach($this->sitemapList as $id => $config) {
            $sitemap = new Sitemap();
            $sitemap->attributes = $config;
            $sitemap->id = $id;
            $sitemap->save();
        }

        foreach($this->seoList as $id => $config) {
            $seo = new Seo();
            $seo->attributes = $config;
            $seo->id = $id;
            $seo->save();
        }

        foreach($this->typeList as $id => $config) {
            $type = new Type();
            $type->attributes = $config;
            $type->id = $id;
            $type->save();
        }

        foreach($this->blocTypeList as $id => $config) {
            $blocType = new BlocType();
            $blocType->attributes = $config;
            $blocType->id = $id;
            $blocType->save();
        }

        foreach($this->typeBlocTypeList as $config) {
            $typeBlocType = new TypeBlocType();
            $typeBlocType->attributes = $config;
            $typeBlocType->save();
        }

        foreach($this->nodeList as $id => $config) {
            $node = new Node();
            $node->attributes = $config;
            $node->id = $id;
            $node->save();
        }

        foreach($this->categoryList as $id => $config) {
            $category = new Category();
            $category->attributes = $config;
            $category->id = $id;
            $category->save();
        }

        foreach($this->tagList as $id => $config) {
            $tag = new Tag();
            $tag->attributes = $config;
            $tag->id = $id;
            $tag->save();
        }

        foreach($this->nodeTagLinks as $config) {
            $link = new NodeTag();
            $link->attributes = $config;
            $link->save();
        }

        foreach($this->blocList as $id => $config) {
            $bloc = new Bloc();
            $bloc->attributes = $config;
            $bloc->id = $id;
            $bloc->save();
        }

        foreach($this->compositeList as $id => $config) {
            $composite = new Composite();
            $composite->attributes = $config;
            $composite->id = $id;
            $composite->save();
        }

        foreach($this->compositeBlocLinks as $config) {
            $link = new CompositeBloc();
            $link->attributes = $config;
            $link->save();
        }

        foreach($this->nodeBlocLinks as $config) {
            $link = new NodeBloc();
            $link->attributes = $config;
            $link->save();
        }

        foreach($this->categoryBlocLinks as $config) {
            $link = new CategoryBloc();
            $link->attributes = $config;
            $link->save();
        }

        foreach($this->tagBlocLinks as $config) {
            $link = new TagBloc();
            $link->attributes = $config;
            $link->save();
        }

        foreach($this->nodeCompositeLinks as $config) {
            $link = new NodeComposite();
            $link->attributes = $config;
            $link->save();
        }

        foreach($this->compositeTagLinks as $config) {
            $link = new CompositeTag();
            $link->attributes = $config;
            $link->save();
        }

    }
}
