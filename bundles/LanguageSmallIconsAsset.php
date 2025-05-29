<?php

namespace econgress\languagepicker\bundles;

use yii\web\AssetBundle;

/**
 * LanguageSmallIcons asset bundle
 *
 * @author Lajos Molnár <econgress.m@gmail.com>
 *
 * @since 1.0
 */
class LanguageSmallIconsAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/econgress/yii2-language-picker/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'stylesheets/language-picker.min.css',
        'stylesheets/flags-small.min.css',
    ];
}
