<?php

namespace app\components;

use yii\validators\Validator;
use yii\helpers\Html;

/**
 * This is a NIT format and value validator
 */
class NitValidator extends Validator
{
	/**
	 * Rule validation for NIT correct value
	 * @param  [class] 	$model    
	 * @param  [string] $attribute      
	 */
	public function validateAttribute($model, $attribute)
	{
    	if ($this->message === "") $this->message = "Formato incorrecto";
		$value = $model->$attribute;
		if (!isset($value) && strlen($value) != 14) {
			$this->addError($model, $attribute, $this->message);
		}
	}

	/**
	 * Rule validation for NIT correct value on ClienSideValidation
	 * @param  [class] 	$model
	 * @param  [string] $attribute
	 * @param  [string] $view
	 * @return [JS]
	 */
	public function clientValidateAttribute($model, $attribute, $view)
    {
    	if ($this->message === "") $this->message = "Formato incorrecto";
        $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return <<<JS
			if (value !== "" && value.length !== 14) {
				messages.push($message);
			}
JS;
    }
}