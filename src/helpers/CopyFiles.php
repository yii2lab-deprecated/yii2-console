<?php

namespace yii2lab\console\helpers;

use yii2lab\console\helpers\input\Question;
use yii2lab\helpers\yii\FileHelper;

class CopyFiles {
	
	const DIR_ACCESS = 0777;
	
	protected $projectConfig;
	protected $isCopyAll = false;
	protected $ignoreNames = [
		'.',
		'..',
	];
	
	public function copyAllFiles($pathFrom, $pathTo = '')
	{
		$files = $this->getFileList(FileHelper::rootPath() . "/$pathFrom");
		$files = $this->filterSkipFiles($files);
		foreach ($files as $file) {
			$source = trim("$pathFrom/$file", '/');
			$to = trim("$pathTo/$file", '/');
			if (!$this->copyFile($source, $to)) {
				break;
			}
		}
	}
	
	public function getFileList($root, $basePath = '')
	{
		$files = [];
		$root = FileHelper::normalizePath($root);
		$handle = opendir($root);
		if(empty($handle)) {
			return [];
		}
		while (($path = readdir($handle)) !== false) {
			if (in_array($path, $this->ignoreNames)) {
				continue;
			}
			$fullPath = "$root/$path";
			$relativePath = $basePath === '' ? $path : "$basePath/$path";
			if (is_dir($fullPath)) {
				$files = array_merge($files, $this->getFileList($fullPath, $relativePath));
			} else {
				$files[] = $relativePath;
			}
		}
		closedir($handle);
		$files = FileHelper::normalizePathList($files);
		return $files;
	}

	private function filterSkipFiles($files)
	{
		if (isset($this->projectConfig['skipFiles'])) {
			$files = array_diff($files, $this->projectConfig['skipFiles']);
		}
		return $files;
	}
	
	private function copyFile($source, $target)
	{
		$source = FileHelper::normalizePath($source);
		$target = FileHelper::normalizePath($target);
		$sourceFile = FileHelper::rootPath() . DIRECTORY_SEPARATOR . $source;
		$targetFile = FileHelper::rootPath() . DIRECTORY_SEPARATOR . $target;
		
		if (!is_file($sourceFile)) {
			Output::line("     skip $target ($source not exist)");
			return true;
		}
		if (is_file($targetFile)) {
			if (FileHelper::isEqualContent($sourceFile, $targetFile)) {
				Output::line("unchanged $target");
				return true;
			}
			if($this->runOverwriteDialog($target)) {
				return true;
			}
			FileHelper::copy($sourceFile, $targetFile, self::DIR_ACCESS);
			return true;
		}
		Output::line("generate $target");
		FileHelper::copy($sourceFile, $targetFile, self::DIR_ACCESS);
		return true;
	}

	private function runOverwriteDialog($target) {
		Output::line("exist $target");
		if ($this->isOverwrite()) {
			Output::line("overwrite $target");
		} else {
			Output::line("skip $target");
			return true;
		}
		return false;
	}

	private function isOverwrite() {
		if($this->isCopyAll) {
			return true;
		}
		$answer = ArgHelper::one('overwrite');
		if(empty($answer)) {
			$answer = Question::display('    ...overwrite?', [
				'y' => 'Yes',
				'n' => 'No',
				'a' => 'All',
				'q' => 'Quit',
			], 'n');
		}
		if($answer == 'q') {
			Output::quit();
		} elseif($answer == 'a') {
			$this->isCopyAll = true;
		}
		return $answer != 'n';
	}
	
}
