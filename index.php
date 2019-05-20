<?php

header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, PATCH, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Vary: Origin');
header('Content-Type: application/json');

if ( ! array_key_exists('key', $_GET)) {
	http_response_code(400);
	echo json_encode([
		'error' => 'No Google Sheet Key',
	]);
	die();
}


require_once './GoogleSpreadsheetToArray.php';
$sheet = new NicoMartin\GoogleSpreadsheetToArray($_GET['key']);
if (array_key_exists('table', $_GET)) {
	$sheet->setTableId(intval($_GET['table']));
}
if (array_key_exists('nocache', $_GET) && $_GET['nocache'] === 'true') {
	$sheet->setCacheTime(0);
}
if (array_key_exists('row', $_GET) && $_GET['row'] === 'true') {
	$sheet->setFirstRowAsKey(true);
}
if (array_key_exists('col', $_GET) && $_GET['col'] === 'true') {
	$sheet->setFirstColAsKey(true);
}
if (array_key_exists('switch', $_GET) && $_GET['switch'] === 'true') {
	$sheet->setKeySwitch(true);
}
echo json_encode($sheet->getArray());
die();
