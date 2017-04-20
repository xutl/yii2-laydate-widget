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

    /**
     * @var boolean If true, shows the widget as an inline calendar and the input as a hidden field.
     */
    public $inline = false;

    public $containerOptions = [];

    public $clientDatetimeFormat;

    /**
     * @var string the model attribute that this widget is associated with.
     * The value of the attribute will be converted using [[\yii\i18n\Formatter::asDate()|`Yii::$app->formatter->asDate()`]]
     * with the [[dateFormat]] if it is not null.
     */
    public $attribute;

    /**
     * @var string the input value.
     * This value will be converted using [[\yii\i18n\Formatter::asDate()|`Yii::$app->formatter->asDate()`]]
     * with the [[dateFormat]] if it is not null.
     */
    public $value;

    /**
     * @var string php datetime Format
     */
    public $datetimeFormat = 'Y-m-d H:i:s';

    /**
     * @var array
     */
    protected $datetimeMapping = [
        "Y-m-d" => 'yyyy-mm-dd', // 2014-05-14 13:55
        "Y-m-d H:i" => 'yyyy-mm-dd hh:ii', // 2014-05-14 13:55
        "Y-m-d H:i:s" => 'yyyy-mm-dd hh:ii:ss', // 2014-05-14 13:55:50
    ];

    public $clientOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->inline && !isset($this->containerOptions['id'])) {
            $this->containerOptions['id'] = $this->options['id'] . '-container';
        }
        $this->options = ArrayHelper::merge([
            'class' => 'form-control',
        ], $this->options);

        $this->clientDatetimeFormat = $this->clientDatetimeFormat ?: ArrayHelper::getValue(
            $this->datetimeMapping,
            $this->datetimeFormat
        );
        $this->clientOptions = ArrayHelper::merge([
            'event' => 'focus',
        ], $this->clientOptions);

        if (!$this->clientDatetimeFormat) {
            throw new InvalidConfigException('Please set datetime format');
        }
        $this->clientOptions['format'] = $this->clientDatetimeFormat;
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo $this->renderWidget() . "\n";

        $containerID = $this->inline ? $this->containerOptions['id'] : $this->options['id'];
        LayDateAsset::register($this->view);
        if(!isset($this->clientOptions['elem'])){
            $this->clientOptions['elem'] = $this->options['id'];
        }
        $options = Json::htmlEncode($this->clientOptions);
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

        if ($this->inline === false) {
            // render a text input
            if ($this->hasModel()) {
                $contents[] = Html::activeTextInput($this->model, $this->attribute, $options);
            } else {
                $contents[] = Html::textInput($this->name, $value, $options);
            }
        } else {
            // render an inline date picker with hidden input
            if ($this->hasModel()) {
                $contents[] = Html::activeHiddenInput($this->model, $this->attribute, $options);
            } else {
                $contents[] = Html::hiddenInput($this->name, $value, $options);
            }
            $this->clientOptions['initialDate'] = $value;
            $this->clientOptions['linkField'] = $this->options['id'];
            $this->clientOptions['linkFormat'] = $this->clientOptions['format'];
            $contents[] = Html::tag('div', null, $this->containerOptions);
        }

        return implode("\n", $contents);
    }
}