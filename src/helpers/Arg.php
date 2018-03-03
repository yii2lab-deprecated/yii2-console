<?php

namespace yii2lab\console\helpers;

use yii\console\Exception;
use yii\console\Request;
use yii2mod\helpers\ArrayHelper;

class Arg {
	
	protected $args = [];
	protected $aliases = [];
	
	public function __construct(array $aliases = []) {
		$this->aliases = $aliases;
		$request = new Request;
		try {
			$this->args = $request->resolve();
		} catch(Exception $e) {
			$this->args = [];
		}
	}
	
	public function resolve() {
		return $this->args;
	}
	
	public function options() {
		$options = [];
		if(empty($this->args)) {
			return $options;
		}
		foreach($this->args as $arg) {
			if(!empty($arg) && !is_array($arg)) {
				if($arg{0} == '-' && $arg{1} != '-') {
					$aliasName = trim($arg, '-');
					$options[] = '--' . $this->aliases[$aliasName];
				} else {
					$options[] = $arg;
				}
			}
		}
		return $options;
	}
	
	public function params() {
		$params = ArrayHelper::last($this->args);
		$aliases = ArrayHelper::getValue($params, '_aliases', []);
		ArrayHelper::remove($params, '_aliases');
		foreach($aliases as $aliasName => $aliasValue) {
			$paramName = $this->aliases[$aliasName];
			$params[$paramName] = $aliasValue;
		}
		return $params;
	}
	
	public function hasOptions($names) {
		$names = ArrayHelper::toArray($names);
		foreach($names as $name) {
			if($this->hasOption($name)) {
				return true;
			}
		}
		return false;
	}
	
	public function hasOption($name) {
		$options = $this->options();
		return in_array('--' . $name, $options);
	}
	
	public function getParam($name, $default = null) {
		$params = $this->params();
		return ArrayHelper::getValue($params, $name, $default);
	}
	
	public function hasParam($name) {
		$params = $this->params();
		return ArrayHelper::has($params, $name);
	}
	
}
