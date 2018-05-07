<?php

namespace yii2lab\console\helpers\input;

use yii2lab\console\helpers\Output;

class Select {

	static function display($message, $options, $isMulti = false, $isCustom = false) {
		self::selectPrint($message, $options, $isMulti, $isCustom);
		$answer = trim(fgets(STDIN));
		if($answer == 'a') {
			return $options;
		} elseif($answer == 'q' || $answer == '') {
			Output::quit();
		} elseif($answer == 'c' && $isCustom) {
			$result = Enter::display('Enter custom value');
			return ['c' => $result];
		}
		$result = null;
		if(!$isMulti) {
			$result = array_key_exists($answer, $options) ? $options[$answer] : null;
		}
		$result = self::selectGetResult($options, $answer);
		
		Output::line('Selected values: ');
		foreach($result as $value) {
			Output::line(' * ' . $value);
		}
		Output::pipe();
		Output::line();
		
		if(empty($result)) {
			exit;
		}
		return $result;
	}

	public static function getFirstValue($answer) {
		return array_values($answer)[0];
	}
	
	public static function getFirstKey($answer) {
		return array_keys($answer)[0];
	}
	
	private static function selectAssign($options, $keys) {
		foreach($keys as $k) {
			$k = trim($k);
			if(array_key_exists($k, $options)) {
				$result[$k] = $options[$k];
			} else {
				Output::pipe('Bad select parameter!');
				exit;
			}
		}
		return $result;
	}

	private static function selectRange($options, $begin, $end) {
		$isStart = false;
		if(!array_key_exists($begin, $options) || !array_key_exists($end, $options)) {
			return [];
		}
		$result = [];
		foreach($options as $k => $v) {
			$k = trim($k);
			if($k == $begin) {
				$isStart = true;
			}
			if($isStart) {
				$result[] = $k;
			}
			if($k == $end) {
				$isStart = false;
			}
		}
		return $result;
	}

	private static function selectGetResult($options, $answer) {
		$answerArray = explode(',', $answer);
		$keyArray = [];
		foreach($answerArray as $key) {
			if(preg_match('/^(\w+)-(\w+)$/', $key, $matches)) {
				$keyRange = self::selectRange($options, $matches[1], $matches[2]);
				$keyArray = array_merge($keyArray, $keyRange);
			} else {
				$keyArray[] = $key;
			}
		}
		return self::selectAssign($options, $keyArray);
	}

	private static function selectPrint($message, $options, $isMulti, $isCustom) {
		echo PHP_EOL;
		if($isMulti) {
			$options['a'] = '[Select all]';
		}
		$options['q'] = '[Quit]';
		if($isCustom) {
			$options['c'] = '[Custom]';
		}
		Output::arr($options, $message);
		echo PHP_EOL;
		echo 'You select: ';
	}

}
