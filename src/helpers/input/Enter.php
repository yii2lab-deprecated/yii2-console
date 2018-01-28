<?php

namespace yii2lab\console\helpers\input;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii2lab\console\helpers\Output;

class Enter {
	
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
			Yii::configure($form, $data);
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
			$rules = $form->rules();
			$attributeType = self::getType($rules, $attributeName);
			
			if($attributeType['type'] == 'string') {
				$value = Enter::display($message);
			} elseif($attributeType['type'] == 'enum') {
				if(ArrayHelper::isIndexed($attributeType['range'])) {
					$value = Select::display($message, $attributeType['range']);
					$key = key($value);
					$value = $value[$key];
				} else {
					$value = Question::display($message, array_values($attributeType['range']));
					$value = ArrayHelper::getValue($attributeType, "range.{$value}");
				}
			} elseif($attributeType['type'] == 'boolean') {
				$value = Question::confirm($message);
			}
			if(!empty($value) || $value === '0' || $value === 0 || $value === false) {
				$form->{$attributeName} = $value;
			}
		}
	}
	
	private static function getType($rules, $attribute) {
		$result = [
			'type' => 'string',
		];
		foreach($rules as $rule) {
			if(self::hasAttribute($rule, $attribute)) {
				if($rule[1] == 'in') {
					$range = $rule['range'];
					$uc = [];
					foreach($range as $item) {
						$firstChar = $item{0};
						$uc[$firstChar] = $item;
					}
					$range = count($uc) == count($range) ? $uc : $range;
					$result = [
						'type' => 'enum',
						'range' => $range,
					];
				} elseif($rule[1] == 'boolean') {
					$result = [
						'type' => 'boolean',
					];
				}
			}
		}
		return $result;
	}
	
	private static function hasAttribute($rule, $attribute) {
		$attributeList = $rule[0];
		$attributeList = ArrayHelper::toArray($attributeList);
		return in_array($attribute, $attributeList);
	}
	
}
