<?php

namespace yii2lab\console\helpers;

use yii\helpers\ArrayHelper;

class ArgHelper {
	
	public static function one($name) {
		$all = self::all();
		return ArrayHelper::getValue($all, $name);
	}
	
	public static function all() {
		$argv = self::getArgs();
		return self::parse($argv);
	}

	private static function parse($argList) {
		$result = [];
		foreach($argList as $arg) {
			list($name, $value) = self::split($arg);
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

	private static function split($arg) {
		$e = explode("=", $arg);
		$name = $e[0];
		$value = count($e) > 1 ? $e[1] : null;
		return [$name, $value];
	}

	private static function getArgs() {
		$argv = [];
		if (isset($_SERVER['argv'])) {
			$argv = $_SERVER['argv'];
			array_shift($argv);
		}
		return $argv;
	}
}
