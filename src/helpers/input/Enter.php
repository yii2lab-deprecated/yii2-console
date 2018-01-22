<?php

namespace yii2lab\console\helpers\input;

use Yii;
use yii\base\Model;
use yii\helpers\Inflector;
use yii2lab\console\helpers\Output;

class Enter {
	
	const ERROR = '{[$ERROR$]}';
	
	static function display($message, $type = 'string') {
		echo PHP_EOL . $message . ': ';
		$answer = trim(fgets(STDIN));
		return $answer;
	}

	static function arr($options) {
		$result = [];
		foreach($options as $message => $type) {
			$result[$message] = self::display(Inflector::titleize($message), $type);
		}
		return $result;
	}
	
	static function form($form, $data = null, $scenario = null) {
		/** @var Model $form */
		if(!is_object($form)) {
			$form = Yii::createObject($form);
		}
		if($data) {
			$form->setAttributes($data);
		}
		if($scenario) {
			$form->scenario = $scenario;
		}
		self::inputAll($form);
		return $form->toArray();
	}
	
	private static function inputAll(Model $form) {
		$only = [];
		do {
			self::formInput($form, $only);
			$isValidate = $form->validate();
			if(!$isValidate) {
				Output::arr($form->getFirstErrors(), 'Validation error');
				$only = array_keys($form->getErrors());
			}
		} while(!$isValidate);
	}
	
	private static function formInput(Model $form, $only = null) {
		$attributeLabels = $form->attributeLabels();
		foreach($form->attributes as $attributeName => $attributeValue) {
			if(!empty($only) && !in_array($attributeName, $only)) {
				continue;
			}
			$message = $attributeLabels[$attributeName];
			if(!empty($attributeValue) && empty($only)) {
				$message .= ' (default: ' . $attributeValue . ')';
			}
			$value = Enter::display($message);
			if(!empty($value)) {
				$form->{$attributeName} = $value;
			}
		}
	}
	
}
