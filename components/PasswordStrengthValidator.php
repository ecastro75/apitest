<?php

namespace app\components;

use yii\validators\Validator;
use yii\helpers\Html;

/**
 * This is a Password strength validator
 */
class PasswordStrengthValidator extends Validator
{
	public $strength;
	private $weak_pattern = '/^(?=.*[a-zA-Z0-9]).{5,}$/';
    private $strong_pattern = '/^(?=.*\d(?=.*\d))(?=.*[a-zA-Z](?=.*[a-zA-Z])).{8,}$/';

	/**
	 * Rule validation for password strength
	 * @param  [class] 	$model    
	 * @param  [string] $attribute      
	 */
	public function validateAttribute($model, $attribute)
	{
		if ($this->strength === "") $this->strength = "weak";
    	if ($this->message === "") $this->message = "La clave es muy debil!";
    	$pattern = $this->weak_pattern;

		// check the strength parameter used in the validation rule of our model
	    if ($this->strength === 'weak') {
	    	$pattern = $this->weak_pattern;	
	    } elseif ($this->strength === 'strong') {
	    	$pattern = $this->strong_pattern;
	    }

	    // extract the attribute value from it's model object
	    $value = $model->$attribute;
	    if($value !== "" && !preg_match($pattern, $value)) {
			$this->addError($model, $attribute, $this->message);
	    }
	}

	/**
	 * Rule validation for password strength on ClienSideValidation
	 * @param  [class] 	$model
	 * @param  [string] $attribute
	 * @param  [string] $view
	 * @return [JS]
	 */
	public function clientValidateAttribute($model, $attribute, $view)
    {
		if ($this->strength === "") $this->strength = "weak";
    	if ($this->message === "") $this->message = "La clave es muy debil!";
    	$pattern = $this->weak_pattern;

		// check the strength parameter used in the validation rule of our model
	    if ($this->strength === 'weak') {
	    	$pattern = $this->weak_pattern;	
	    } elseif ($this->strength === 'strong') {
	    	$pattern = $this->strong_pattern;
	    }

    	$condition = "value !== '' && !value.match(". $pattern .")";
        $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return <<<JS
			if ($condition) {
				messages.push($message);
			}
JS;
    }
}