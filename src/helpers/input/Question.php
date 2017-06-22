<?php

namespace yii2lab\console\helpers\input;

use yii2lab\console\helpers\Output;

class Question {

	static function display($message, $options = ['Yes', 'No'], $defaultAnswer = null, $answer = null) {
		$assocOptions = self::confirmGetOptions($options);
		self::printMessageAndOptions($message, $assocOptions);
		return self::getAnswer($assocOptions, $answer, $defaultAnswer);
	}

	static function displayWithQuit($message, $options = ['Yes', 'No'], $answer = null) {
		$options[] = 'Quit';
		$answer = self::display($message, $options, 'q', $answer);
		if($answer == 'q') {
			Output::quit();
		}
		return $answer;
	}

	static function confirm($message = null, $doExit = false) {
		$message = $message ? $message : 'Are you sure?';
		$answer = self::display($message, ['Yes', 'No']) == 'y';
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

	private static function printMessageAndOptions($question, $assocOptions) {
		echo PHP_EOL . $question . ' [' . implode('|', $assocOptions) . ']: ';
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
