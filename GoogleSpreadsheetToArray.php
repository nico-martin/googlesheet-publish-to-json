<?php

namespace NicoMartin;

class GoogleSpreadsheetToArray
{
	private $url = '';
	private $sheetId = 0;
	private $sheetKey = false;

	private $cacheDir = '';
	private $cacheTime = 60 * 10; // 10 Minutes

	private $rowAsKey = false;
	private $colAsKey = false;
	private $keySwitch = false;

	public function __construct($key)
	{
		$this->sheetKey = $key;
		$this->updateUrl();
		$this->cacheDir = dirname(__FILE__) . '/cache/';
		if ( ! is_dir($this->cacheDir)) {
			mkdir($this->cacheDir);
		}
	}

	/**
	 * Setters
	 */

	public function setSheetId($id)
	{
		$this->sheetId = intval($id);
		$this->updateUrl();
	}

	public function setCacheTime($time)
	{
		$this->cacheTime = intval($time);
	}

	public function setFirstRowAsKey(bool $boolean)
	{
		$this->rowAsKey = $boolean;
	}

	public function setFirstColAsKey(bool $boolean)
	{
		$this->colAsKey = $boolean;
	}

	public function setKeySwitch(bool $boolean)
	{
		$this->keySwitch = $boolean;
	}

	public function updateUrl()
	{
		$this->url = 'https://docs.google.com/spreadsheets/d/e/' . $this->sheetKey . '/pub?gid=' . $this->sheetId . '&single=true&output=tsv';
	}

	/**
	 * Getters
	 */

	public function getArray()
	{

		$cacheFile = $this->cacheDir . $this->sheetKey . '-' . $this->sheetId . '.json';
		if (file_exists($cacheFile) && filemtime($cacheFile) >= time() - $this->cacheTime) {
			return json_decode(file_get_contents($cacheFile));
		}

		$data = $this->remoteGet($this->url);
		if ( ! $data) {
			http_response_code(500);

			return false;
		}
		$parsed = $this->parse($data);
		file_put_contents($cacheFile, json_encode($parsed));

		return $parsed;
	}

	/**
	 * Misc
	 */

	private function parse($data)
	{
		$rows       = explode("\r\n", $data);
		$full_array = [];
		$firstRow   = $this->makeArrayValsUnique(array_map([$this, 'sanitizeKey'], explode("\t", $rows[0])));
		$firstCol   = [];
		foreach ($rows as $row) {
			$firstCol[] = explode("\t", $row)[0];
		}
		$firstCol = $this->makeArrayValsUnique(array_map([$this, 'sanitizeKey'], $firstCol));
		if ($this->rowAsKey) {
			unset($rows[0]);
		}

		$rowI = 0;
		foreach ($rows as $rowIndex => $row) {
			$elements = array_map([$this, 'sanitizeKey'], explode("\t", $row));
			$rowKey   = $rowI;
			if ($this->colAsKey) {
				$rowKey = $firstCol[$rowI];
				unset($elements[0]);
			}
			foreach ($elements as $colIndex => $element) {
				$colKey = ($this->rowAsKey ? $firstRow[$colIndex] : $colIndex);
				if ($this->keySwitch) {
					$full_array[$colKey][$rowKey] = $element;
				} else {
					$full_array[$rowKey][$colKey] = $element;
				}
			}

			$rowI++;
		}

		return $full_array;
	}

	private function remoteGet($url)
	{
		return file_get_contents($url);
	}

	public function sanitizeKey($string)
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

	public function makeArrayValsUnique($array)
	{
		$newArray = [];
		foreach ($array as $val) {
			$i        = 0;
			$isUnique = false;
			$valRaw   = $val;
			while ( ! $isUnique) {
				$val = ($i == 0 ? $valRaw : $valRaw . '-' . $i);
				if ( ! in_array($val, $newArray)) {
					$isUnique = true;
				}
				$i++;
			}
			$newArray[] = $val;
		}

		return $newArray;
	}
}
