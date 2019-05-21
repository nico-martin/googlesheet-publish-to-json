# GoogleSheet Publish to JSON

A simple, ready to use Tool to parse a "Publish to web" Google Spreadsheet and return ist as JSON.

## Example
I have a google Sheet for some of my past talks:  
https://docs.google.com/spreadsheets/d/1trRcmLTu0Ey_bbyg5vWWVOYqjXnzG_ScVw1IzdUOx5o/edit?usp=sharing

Now I'd like to get this from an API. The "File" -> "Publish to web" looks like this:  
https://docs.google.com/spreadsheets/d/e/2PACX-1vSNjZItcRIaqBeN8xIBQNjphBUqgBEOo149_bUjFMLOGsByT0LXqaBF3C-zN44ThrDeEdB5Q_bJsW5B/pub?gid=0&single=true&output=csv

The Sheet key in this case is `2PACX-1vSNjZItcRIaqBeN8xIBQNjphBUqgBEOo149_bUjFMLOGsByT0LXqaBF3C-zN44ThrDeEdB5Q_bJsW5B`.

So in my demo installation I can use the following link to have a JSON endpoint:
```
https://google-sheets.nico.dev/2PACX-1vSNjZItcRIaqBeN8xIBQNjphBUqgBEOo149_bUjFMLOGsByT0LXqaBF3C-zN44ThrDeEdB5Q_bJsW5B/
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
* `$sheet->setTableId({int})`: If the Spreadsheet has multiple tables you can pass the requested table
* `$sheet->setFirstColAsKey(bool $boolean)`: Use the first Column of the Spreadsheet as a JSON/Array Key
* `$sheet->setFirstRowAsKey(bool $boolean)`: Use the first Row of the Spreadsheet as a JSON/Array Key
* `$sheet->setKeySwitch(bool $boolean)`: Switch Up the Array so it uses the Row as the first level array instead of the Col
* `$sheet->setCacheTime({time in sec})`: Set the Cachetime (defaults to 600 - 10 Minutes)
* `$sheet->setAllowedKeys({keys})`: Limit the allowed keys. Can be one key or an array or keys
