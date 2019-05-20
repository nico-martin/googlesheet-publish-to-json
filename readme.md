# GoogleSheet Publish to JSON

A simple, ready to use Tool to parse a "Publish to web" Google Spreadsheet and return ist as JSON.

## Example
```
https://example.dev/{{ publish to web Key}}/
```
or without the rewrite rules from `./.htaccess`:
```
https://example.dev/index.php?key={{ publish to web Key}}
```

## Params
* `table={int}`: If the Spreadsheet has multiple tables you can pass the requested table
* `col=true`: Use the first Column of the Spreadsheet as a JSON/Array Key
* `row=true`: Use the first Row of the Spreadsheet as a JSON/Array Key
* `switch=true`: Switch Up the Array so it uses the Row as the first level array instead of the Col
* `nocache=true`: Bypass the cache (sets the cachetime to 0)

## Class GoogleSpreadsheetToArray
### Simple example
```php
require_once './GoogleSpreadsheetToArray.php';
$publishToWebKey = 'key';
$sheet = new NicoMartin\GoogleSpreadsheetToArray($publishToWebKey);
// Additional Setters
$array = $sheet->getArray();
```

### Setters
* `setTableId({int})`: If the Spreadsheet has multiple tables you can pass the requested table
* `setFirstColAsKey(bool $boolean)`: Use the first Column of the Spreadsheet as a JSON/Array Key
* `setFirstRowAsKey(bool $boolean)`: Use the first Row of the Spreadsheet as a JSON/Array Key
* `setKeySwitch(bool $boolean)`: Switch Up the Array so it uses the Row as the first level array instead of the Col
* `setCacheTime({time in sec})`: Set the Cachetime (defaults to 600 - 10 Minutes)
