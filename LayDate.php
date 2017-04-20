<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\laydate;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\widgets\InputWidget;
use yii\base\InvalidParamException;
use yii\base\InvalidConfigException;

/**
 * Class DatetimePicker
 * @package xutl\bootstrap\datetimepicker
 */
class LayDate extends InputWidget
{
    public $clientOptions = [];

    public $skin;

    /**
     * @var string php datetime Format
     */
    public $datetimeFormat = 'Y-m-d';

    /**
     * @var array
     */
    protected $datetimeMapping = [
        "Y-m-d" => 'YYYY-MM-DD', // 2014-05-14 13:55
        "Y-m-d H:i" => 'YYYY-MM-DD hh:mm', // 2014-05-14 13:55
        "Y-m-d H:i:s" => 'YYYY-MM-DD hh:mm:ss', // 2014-05-14 13:55:50
    ];


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!isset ($this->options ['id'])) {
            $this->options ['id'] = $this->getId();
        }
        $clientDatetimeFormat = ArrayHelper::getValue(
            $this->datetimeMapping,
            $this->datetimeFormat
        );
        $this->clientOptions = ArrayHelper::merge([
            'elem' => '#' . $this->options ['id'],
            'event' => 'focus',
        ], $this->clientOptions);
        $this->clientOptions['format'] = $clientDatetimeFormat;
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo $this->renderWidget() . "\n";

        LayDateAsset::register($this->view);
        $options = Json::htmlEncode($this->clientOptions);
        if ($this->skin) {
            $this->view->registerJs("laydate.skin('{$this->skin}');");
        }
        $this->view->registerJs("laydate({$options});");
    }

    /**
     * Renders the DatePicker widget.
     * @return string the rendering result.
     */
    protected function renderWidget()
    {
        $contents = [];

        // get formatted date value
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }
        if ($value !== null && $value !== '') {
            // format value according to dateFormat
            try {
                $value = Yii::$app->formatter->asDatetime($value, 'php:' . $this->datetimeFormat);
            } catch (InvalidParamException $e) {
                // ignore exception and keep original value if it is not a valid date
            }
        }
        $options = $this->options;
        $options['value'] = $value;
        // render a text input
        if ($this->hasModel()) {
            $contents[] = Html::activeTextInput($this->model, $this->attribute, $options);
        } else {
            $contents[] = Html::textInput($this->name, $value, $options);
        }
        return implode("\n", $contents);
    }
}