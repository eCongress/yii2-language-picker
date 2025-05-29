<?php

namespace econgress\languagepicker\bundles;

use yii\web\AssetBundle;

/**
 * LanguagePlugin asset bundle
 *
 * @author Lajos Molnár <econgress.m@gmail.com>
 *
 * @since 1.0
 */
class LanguagePluginAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/econgress/yii2-language-picker/assets';

    /**
     * @inheritdoc
     */
    public $js = [
        'javascripts/language-picker.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
