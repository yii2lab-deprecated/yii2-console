<?php

namespace yii2lab\console\helpers;

use yii\helpers\ArrayHelper;

// todo: move to console vendor

class ParameterHelper {
	
	public static function one($name) {
		$all = self::all();
		return ArrayHelper::getValue($all, $name);
	}
	
	public static function all() {
		$argv = [];
		if (isset($_SERVER['argv'])) {
			$argv = $_SERVER['argv'];
			array_shift($argv);
		}
		$result = [];
		foreach($argv as $arg) {
			$e = explode("=", $arg);
			$name = $e[0];
			$value = count($e) > 1 ? $e[1] : null;
			if(preg_match('/(.+)\[(.+)\]\[(.+)\]/', $name, $matches)) {
				$result[ $matches[1] ] [ $matches[2] ] [ $matches[3] ] = $value;
			} elseif(preg_match('/(.+)\[(.+)\]/', $name, $matches)) {
				$result[ $matches[1] ] [ $matches[2] ] = $value;
			} else {
				$result[ $name ] = $value;
			}
		}
		return $result;
	}
	
}
