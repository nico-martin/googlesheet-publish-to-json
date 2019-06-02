<?php

namespace NicoMartin;

class Filter
{
	private $string = '';
	private $allowedUserFuncs = [
		'intval',
		'floatval',
		'boolval',
	];

	public function __construct($string)
	{
		$this->string = $string;
	}

	public function filter($filter)
	{
		if (in_array($filter, $this->allowedUserFuncs)) {
			$this->string = call_user_func($filter, $this->string);
		} elseif ($filter == 'md2html') {
			require_once 'Vendor/Parsedown.php';
			$Parsedown    = new \Parsedown();
			$this->string = $Parsedown->text($this->string);
		} elseif ($filter == 'key') {
			$this->string = $this->sanitizeKey($this->string);
		}
	}

	public function getContent()
	{
		return $this->string;
	}

	/**
	 * Filters
	 */

	private function sanitizeKey($string)
	{
		$string = trim($string);
		$string = strtolower($string);
		$string = str_replace([' ', '_', '/', '\\', '.'], '-', $string);

		$string = str_replace('ä', 'ae', $string);
		$string = str_replace('ö', 'oe', $string);
		$string = str_replace('ü', 'ue', $string);

		$string = preg_replace("/[^a-z0-9-]/", '', $string);
		$string = preg_replace("/(-{2,})/", '-', $string); // replace multiple - with one
		$string = preg_replace("/(^-|-$)/", '', $string); // remove - from start and end

		return $string;
	}
}