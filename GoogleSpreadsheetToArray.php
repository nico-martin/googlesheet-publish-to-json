<?php

namespace NicoMartin;

class GoogleSpreadsheetToArray
{
	private $url = '';
	private $tableId = 0;
	private $sheetKey = false;
	private $allowedKeys = [];

	private $cacheDir = '';
	private $cacheTime = 60 * 10; // 10 Minutes

	private $rowAsKey = false;
	private $colAsKey = false;
	private $keySwitch = false;
	private $reverseEntries = false;

	private $filter = [];

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

	public function setTableId($id)
	{
		$this->tableId = intval($id);
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

	public function setAllowedKeys($keys)
	{
		if (is_string($keys)) {
			$this->allowedKeys[] = $keys;
		} elseif (is_array($keys)) {
			foreach ($keys as $key) {
				$this->allowedKeys[] = $key;
			}
		}
	}


	public function setFilter(string $type = 'col', string $col, array $filters)
	{
		$this->filter[$type][$col] = $filters;
	}

	public function setReverseEntries(bool $boolean)
	{
		$this->reverseEntries = $boolean;
	}

	public function updateUrl()
	{
		$this->url = 'https://docs.google.com/spreadsheets/d/e/' . $this->sheetKey . '/pub?gid=' . $this->tableId . '&single=true&output=csv';
	}

	/**
	 * Getters
	 */

	public function getArray()
	{
		if ( ! empty($this->allowedKeys) && ! in_array($this->sheetKey, $this->allowedKeys)) {
			return false;
		}

		$filter = md5(json_encode($this->filter));

		$cacheFile = "{$this->cacheDir}{$this->sheetKey}-{$this->tableId}-{$this->rowAsKey}-{$this->colAsKey}-{$this->keySwitch}-{$filter}.json";
		if (file_exists($cacheFile) && filemtime($cacheFile) >= time() - $this->cacheTime) {
			return json_decode(file_get_contents($cacheFile));
		}

		$data   = $this->remoteGet($this->url);
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
		$firstRow   = $this->makeArrayValsUnique(array_map([$this, 'sanitizeKey'], str_getcsv($rows[0])));
		$firstCol   = [];
		foreach ($rows as $row) {
			$firstCol[] = str_getcsv($row)[0];
		}
		$firstCol = $this->makeArrayValsUnique(array_map([$this, 'sanitizeKey'], $firstCol));

		$rowI = 0;
		foreach ($rows as $rowIndex => $row) {
			$elements = str_getcsv($row);
			$rowKey   = $rowI;
			if ($this->colAsKey) {
				$rowKey = $firstCol[$rowI];
			}
			foreach ($elements as $colIndex => $element) {
				$colKey = ($this->rowAsKey ? $firstRow[$colIndex] : $colIndex);
				if ($this->keySwitch) {
					$full_array[$colKey][$rowKey] = $this->mayBeDoFilter('col', $colKey, $this->mayBeDoFilter('row', $rowKey, $element));
				} else {
					$full_array[$rowKey][$colKey] = $this->mayBeDoFilter('row', $rowKey, $this->mayBeDoFilter('col', $colKey, $element));
				}
			}

			$rowI++;
		}

		if ($this->rowAsKey) {
			if ($this->keySwitch) {
				foreach ($full_array as $key => $array) {
					array_shift($full_array[$key]);
				}
			} else {
				array_shift($full_array);
			}
		}

		if ($this->colAsKey) {
			if ($this->keySwitch) {
				array_shift($full_array);
			} else {
				foreach ($full_array as $key => $array) {
					array_shift($full_array[$key]);
				}
			}
		}

		if ($this->reverseEntries) {
			return array_reverse($full_array);
		}

		return $full_array;
	}

	private function remoteGet($url)
	{
		return file_get_contents($url);
	}

	public function sanitizeKey($string)
	{
		require_once './helpers/Filter.php';
		$filtered = new Filter($string);
		$filtered->filter('key');

		return $filtered->getContent();
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

	public function mayBeDoFilter($type, $key, $content)
	{
		if ( ! array_key_exists($type, $this->filter)) {
			return $content;
		}

		if ( ! array_key_exists($key, $this->filter[$type])) {
			return $content;
		}

		require_once './helpers/Filter.php';
		$filtered = new Filter($content);

		foreach ($this->filter[$type][$key] as $filter) {
			$filtered->filter($filter);
		}

		return $filtered->getContent();
	}
}
