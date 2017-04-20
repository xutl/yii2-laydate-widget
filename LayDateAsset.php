<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\laydate;

use Yii;
use yii\web\AssetBundle;

/**
 * Class DatetimePickerAsset
 * @package xutl\bootstrap\datetimepicker
 */
class LayDateAsset extends AssetBundle
{
    /**
     * @inherit
     */
    public $sourcePath = '@vendor/xutl/yii2-laydate-widget/assets';

    /**
     * @inherit
     */
    public $js = [
        'laydate.js',
    ];

}