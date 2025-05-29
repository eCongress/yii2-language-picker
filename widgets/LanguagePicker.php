<?php

namespace econgress\languagepicker\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * Language Picker widget.
 *
 * Examples:
 * Pre-defined button list:
 *
 * ~~~
 * \econgress\languagepicker\widgets\LanguagePicker::widget([
 *      'skin' => \econgress\languagepicker\widgets\LanguagePicker::SKIN_BUTTON,
 *      'size' => \econgress\languagepicker\widgets\LanguagePicker::SIZE_SMALL
 * ]);
 * ~~~
 *
 * Pre-defined DropDown list:
 *
 * ~~~
 *  \econgress\languagepicker\widgets\LanguagePicker::widget([
 *      'skin' => \econgress\languagepicker\widgets\LanguagePicker::SKIN_DROPDOWN,
 *      'size' => \econgress\languagepicker\widgets\LanguagePicker::SIZE_LARGE
 * ]);
 * ~~~
 *
 * Defining your own template:
 *
 * ~~~
 *  \econgress\languagepicker\widgets\LanguagePicker::widget([
 *      'itemTemplate' => '<li><a href="{link}"><i class="{language}" title="{language}"></i> {name}</a></li>',
 *      'activeItemTemplate' => '<a href="{link}" title="{language}"><i class="{language}"></i> {name}</a>',
 *      'parentTemplate' => '<div class="language-picker dropdown-list {size}"><div>{activeItem}<ul>{items}</ul></div></div>',
 *
 *      'languageAsset' => 'econgress\languagepicker\bundles\LanguageLargeIconsAsset',      // StyleSheets
 *      'languagePluginAsset' => 'econgress\languagepicker\bundles\LanguagePluginAsset',    // JavasSripts
 * ]);
 * ~~~
 *
 *
 * @author Lajos Molnar <econgress.m@gmail.com>
 *
 * @since 1.0
 */
class LanguagePicker extends \yii\base\Widget
{
    /**
     * Type of pre-defined skins (drop down list).
     */
    const SKIN_DROPDOWN = 'dropdown';

    /**
     * Type of pre-defined skins (button list).
     */
    const SKIN_BUTTON = 'button';

    /**
     * Size of pre-defined skins (small).
     */
    const SIZE_SMALL = 'small';

    /**
     * Size of pre-defined skins (large).
     */
    const SIZE_LARGE = 'large';

    /**
     * @var array List of pre-defined skins.
     */
    private $_SKINS = [
        self::SKIN_DROPDOWN => [
            'itemTemplate' => '<li><a href="{link}" title="{language}"><i class="{language}"></i> {name}</a></li>',
            'activeItemTemplate' => '<a href="" title="{language}"><i class="{language}"></i> {name}</a>',
            'parentTemplate' => '<div class="language-picker dropdown-list {size}"><div>{activeItem}<ul>{items}</ul></div></div>',
        ],
        self::SKIN_BUTTON => [
            'itemTemplate' => '<a href="{link}" title="{language}"><i class="{language}"></i> {name}</a>',
            'activeItemTemplate' => '<a href="{link}" title="{language}" class="active"><i class="{language}"></i> {name}</a>',
            'parentTemplate' => '<div class="language-picker button-list {size}"><div>{items}</div></div>',
        ],
    ];

    /**
     * @var array List of pre-defined skins.
     */
    private $_SIZES = [
        self::SIZE_SMALL => 'econgress\languagepicker\bundles\LanguageSmallIconsAsset',
        self::SIZE_LARGE => 'econgress\languagepicker\bundles\LanguageLargeIconsAsset',
    ];

    /**
     * @var string ID of pre-defined skin (optional).
     */
    public $skin;

    /**
     * @var string size of the icons.
     */
    public $size;

    /**
     * @var string The structure of the parent template.
     */
    public $parentTemplate;

    /**
     * @var string The structure of one entry in the list of language elements.
     */
    public $itemTemplate;

    /**
     * @var string The structure of the active language element.
     */
    public $activeItemTemplate;

    /**
     * @var string Adding StyleSheet and its dependencies.
     */
    public $languageAsset;

    /**
     * @var string Adding JavaScript and its dependencies.
     * Changing languages is done through Ajax by default. If you do not wish to use Ajax, set value to null.
     */
    public $languagePluginAsset = 'econgress\languagepicker\bundles\LanguagePluginAsset';

    /**
     * @var array List of available languages.
     *  Formats supported in the pre-defined skins:
     *
     * ~~~
     *  ['en', 'de', 'es']
     *  ['en' => 'English', 'de' => 'Deutsch', 'fr' => 'Français']
     *  ['en-US', 'de-DE', 'fr-FR']
     *  ['en-US' => 'English', 'de-DE' => 'Deutsch', 'fr-FR' => 'Français']
     * ~~~
     */
    public $languages;

    /**
     * @var bool whether to HTML-encode the link labels.
     */
    public $encodeLabels = true;

    /**
     * @inheritdoc
     */
    public static function widget($config = [])
    {
        if (empty($config['languages']) || !is_array($config['languages'])) {
            $config['languages'] = Yii::$app->languagepicker->languages;
        }

        return parent::widget($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_initSkin();

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $isInteger = is_int(key($this->languages));
        if ($isInteger) {
            $this->languages = array_flip($this->languages);
        }
        
        switch ($this->skin) {
            case self::SKIN_BUTTON:
                $languagePicker = $this->_renderButton($isInteger);
                break;
                
            case self::SKIN_DROPDOWN:
                $languagePicker = $this->_renderDropdown($isInteger);
                break;
                
            default:
                throw new InvalidConfigException("Skin '{$this->skin}' no está soportado.");
        }
        
        return $languagePicker;
    }
    

    /**
     * Rendering button list.
     *
     * @param bool $isInteger
     *
     * @return string
     */
    private function _renderButton($isInteger)
    {
        $items = '';
        foreach ($this->languages as $language => $name) {
            $name = $isInteger ? '' : $name;
            $template = Yii::$app->language == $language ? $this->activeItemTemplate : $this->itemTemplate;
            $items .= $this->renderItem($language, $name, $template);
        }

        return strtr($this->parentTemplate, ['{items}' => $items, '{size}' => $this->size]);
    }

    /**
     * Rendering dropdown list.
     *
     * @param bool $isInteger
     *
     * @return string
     */
    protected function _renderDropdown($isInteger)
    {
        $items = [];
        $flagMap = [
            'en' => 'us',
            'es' => 'es',
            'pt-br' => 'br',
        ];
        
        $currentLang = strtolower(Yii::$app->language);
        $currentFlag = $flagMap[$currentLang] ?? 'us';
        
        foreach ($this->languages as $code => $config) {
            if ($isInteger) {
                $code = $config;
                $label = \Locale::getDisplayLanguage($code, $code);
                $flag = $flagMap[$code] ?? strtolower($code);
            } elseif (is_array($config)) {
                $label = $config['label'] ?? $code;
                $flag = $config['icon'] ?? ($flagMap[$code] ?? strtolower($code));
            } else {
                $label = $config;
                $flag = $flagMap[$code] ?? strtolower($code);
            }
            
            $isActive = (Yii::$app->language === $code || strtolower(Yii::$app->language) === strtolower($code));
            
            $items[] = Html::a(
                "<span class='fi fi-{$flag} me-2'></span>" . Html::encode($label),
                Url::to(array_merge(
                    ['/' . ltrim(Yii::$app->controller->getRoute(), '/')],
                    Yii::$app->request->getQueryParams(),
                    ['language' => $code]
                    )),
                    [
                        'class' => 'dropdown-item' . ($isActive ? ' active' : ''),
                        'aria-current' => $isActive ? 'true' : null,
                    ]
                    );
        }
        
        return Html::tag('div',
            Html::button(
                "<span class='fi fi-{$currentFlag}'></span>",
                [
                    'class' => 'btn btn-dark dropdown-toggle',
                    'data-bs-toggle' => 'dropdown',
                    'aria-expanded' => 'false'
                ]
                ) .
                Html::tag('div', implode('', $items), ['class' => 'dropdown-menu dropdown-menu-end']),
                ['class' => 'dropdown']
            );
    }
    
    
    

    /**
     * Initialising skin.
     */
    private function _initSkin()
    {
        if ($this->skin && empty($this->_SKINS[$this->skin])) {
            throw new \yii\base\InvalidConfigException('The skin does not exist: ' . $this->skin);
        }

        if ($this->size && empty($this->_SIZES[$this->size])) {
            throw new \yii\base\InvalidConfigException('The size does not exist: ' . $this->size);
        }

        if ($this->skin) {
            foreach ($this->_SKINS[$this->skin] as $property => $value) {
                if (!$this->$property) {
                    $this->$property = $value;
                }
            }
        }

        if ($this->size) {
            $this->languageAsset = $this->_SIZES[$this->size];
        }

        $this->_registerAssets();
    }

    /**
     * Adding Assets files to view.
     */
    private function _registerAssets()
    {
        if ($this->languageAsset) {
            $this->view->registerAssetBundle($this->languageAsset);
        }

        if ($this->languagePluginAsset) {
            $this->view->registerAssetBundle($this->languagePluginAsset);
        }
    }

    /**
     * Rendering languege element.
     *
     * @param string $language The property of a given language.
     * @param string $name The property of a language name.
     * @param string $template The basic structure of a language element of the displayed language picker
     * Elements to replace: "{link}" URL to call when changing language.
     *  "{name}" name corresponding to a language element, e.g.: English
     *  "{language}" unique identifier of the language element. e.g.: en, en-US
     *
     * @return string the rendered result
     */
    protected function renderItem($language, $name, $template)
    {
        if ($this->encodeLabels) {
            $language = Html::encode($language);
            $name = Html::encode($name);
        }

        $params = array_merge([''], Yii::$app->request->queryParams, ['language-picker-language' => $language]);

        return strtr($template, [
            '{link}' => Url::to($params),
            '{name}' => $name,
            '{language}' => $language,
        ]);
    }
}
