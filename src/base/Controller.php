<?php

namespace yii2lab\console\base;

class Controller extends \yii\console\Controller
{
	
	public function options($actionID)
	{
		$class_vars = get_object_vars($this);
		$class_vars = array_keys($class_vars);
		$key = array_search('interactive', $class_vars);
		array_splice($class_vars, $key);
		return $class_vars;
	}
	
}
