<?php

namespace yii2lab\console\helpers\input;

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
		foreach($options as $name => $type) {
			$result[$name] = self::display($name, $type);
		}
		return $result;
	}

}
