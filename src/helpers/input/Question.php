<?php

namespace yii2lab\console\helpers\input;

use yii2lab\console\helpers\Output;

class Question {

	static function display($message, $options = ['yes', 'no'], $defaultAnswer = null, $answer = null) {
		Output::line();
		$assocOptions = self::confirmGetOptions($options);
		self::printMessageAndOptions($message, $assocOptions, $defaultAnswer);
		$answer = self::getAnswer($assocOptions, $answer, $defaultAnswer);
		return $answer;
	}

	static function displayWithQuit($message, $options = ['yes', 'no'], $answer = null) {
		$options[] = 'Quit';
		$answer = self::display($message, $options, 'q', $answer);
		if($answer == 'q') {
			Output::quit();
		}
		return $answer;
	}
	
	static function confirm2($message = null, $defaultAnswer = null) {
		if($defaultAnswer !== null) {
			$defaultAnswer = $defaultAnswer === true ? 'yes' : 'no';
		}
		$message = $message ? $message : 'Are you sure?';
		$answer = self::display($message, ['yes', 'no'], $defaultAnswer) == 'y';
		return $answer;
	}
	
	static function confirm($message = null, $doExit = false) {
		$message = $message ? $message : 'Are you sure?';
		$answer = self::display($message, ['yes', 'no']) == 'y';
		if($doExit && !$answer) {
			Output::quit();
		}
		return $answer;
	}

	private static function getAnswer($assocOptions, $answer = null, $defaultAnswer = null) {
		$answer =  trim($answer);
		$answer =  $answer ? $answer : trim(fgets(STDIN));
		if(empty($answer)) {
			return $defaultAnswer;
		}
		foreach($assocOptions as $key => $title) {
			if (!strncasecmp($answer, $key, 1)) {
				return $key;
			}
		}
		return $defaultAnswer;
	}

	private static function printMessageAndOptions($question, $assocOptions, $defaultAnswer = null) {
		$default = $defaultAnswer !== null ? ' ['.$defaultAnswer.']' : '';
		echo $question . ' (' . implode('|', $assocOptions) . ')' . $default . ': ';
		//(yes|no) [no]
	}

	private static function confirmGetOptions($options) {
		$assocOptions = [];
		foreach($options as $key => $title) {
			if(is_int($key)) {
				$key = strtolower($title[0]);
			}
			$assocOptions[$key] = $title;
		}
		return $assocOptions;
	}

}
