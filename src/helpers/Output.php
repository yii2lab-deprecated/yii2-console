<?php

namespace yii2lab\console\helpers;

use yii2lab\helpers\StringHelper;

class Output {

	const lineLen = 78;
	
	static function getDots($text, $reservedLength, $char = DOT) {
		$packageNameLen = strlen($text);
		$dots = str_repeat($char, $reservedLength - $packageNameLen);
		return $dots;
	}
	
	static function autoWrap($text, $maxLineLength = self::lineLen) {
		$words = StringHelper::textToArray($text);
		$line = [];
		foreach($words as $word) {
			$lineStr = trim(implode(SPC, $line));
			$lenWithNewWord = strlen($lineStr) + strlen(SPC) + strlen($word);
			if($lenWithNewWord > $maxLineLength) {
				$a[] = $lineStr;
				$line = [];
			}
			$line[] = $word;
		}
		if(!empty($line)) {
			$a[] = implode(SPC, $line);
		}
		self::line(implode(PHP_EOL, $a));
	}
	
	static function quit() {
		self::pipe('Quit');
		exit;
	}

	static function line($data = '', $newLine = 'after') {
		if($newLine == 'before' || $newLine == 'both') {
			echo PHP_EOL;
		}
		echo $data;
		if($newLine == 'after' || $newLine == 'both') {
			echo PHP_EOL;
		}
	}

	static function pipe($title = '', $charBorder = '-') {
		$title = trim($title);
		$title = mb_substr($title, 0, 72);
		$titleLen = mb_strlen($title);
		if($titleLen > 0) {
			$lenForPipe = self::lineLen - $titleLen - 2;
			$pipeLen =  intval($lenForPipe / 2);
			$pipe =  str_repeat($charBorder, $pipeLen) . ' ' . $title . ' ' . str_repeat($charBorder, $pipeLen);
			if(mb_strlen($pipe) < self::lineLen) {
				$pipe .= $charBorder;
			}
		} else {
			$pipe = str_repeat($charBorder, self::lineLen);
		}
		echo $pipe;
		echo PHP_EOL;
	}

	static function block($data, $title = '', $charBorder = '-', $charWrap = '') {
		$consoleLen = self::lineLen - 2 - (mb_strlen($charWrap) * 2);
		$dataArr = str_split($data, $consoleLen);
		$tmp = '';
		foreach($dataArr as $str) {
			$tmp .= self::_item($str, $charWrap);
		}
		echo PHP_EOL;
		self::pipe($title, $charBorder);
		//echo PHP_EOL;
		echo $tmp;
		echo PHP_EOL;
		self::pipe('', $charBorder);
	}

	private static function list_item($data) {
		$data = trim($data);
		$data = mb_substr($data, 0, 72);
		echo ' * ' . $data . PHP_EOL;
	}

	private static function item($data) {
		$data = trim($data);
		$data = mb_substr($data, 0, 76);
		echo ' ' . $data . PHP_EOL;
	}

	static function items($data, $title = '', $charBorder = '-') {
		self::pipe($title, $charBorder);
		//echo PHP_EOL;
		if(!empty($data)) {
			foreach($data as $item) {
				self::list_item($item);
			}
		}
		self::pipe('', $charBorder);
	}

	static function arr($data, $title = '', $charBorder = '-') {
		self::pipe($title, $charBorder);
		//echo PHP_EOL;
		foreach($data as $key => $item) {
			self::item($key . ': ' . $item);
		}
		self::pipe('', $charBorder);
	}

	private static function _item($str, $charWrap = '|') {
		$consoleLen = self::lineLen - 2 - (mb_strlen($charWrap) * 2);
		$len = mb_strlen($str);
			$spacesLen = $consoleLen - $len;
			$spaces = '';
			if($spacesLen > 0) {
				$spaces = str_repeat(' ', abs($spacesLen));
			}
			return $charWrap . ' ' . $str . $spaces . ' ' . $charWrap;
	}

}
