# GoogleSheet Publish to JSON

A simple, ready to use PHP-based Tool to parse a "Publish to web" Google Spreadsheet and return ist as JSON.

## Example
I have a google sheet for some of my past talks and now I'd like to get this as a JSON.
```
https://docs.google.com/spreadsheets/d/1trRcmLTu0Ey_bbyg5vWWVOYqjXnzG_ScVw1IzdUOx5o/edit?usp=sharing
```

The "File" -> "Publish to web" looks like this:  
```
https://docs.google.com/spreadsheets/d/e/2PACX-1vSNjZItcRIaqBeN8xIBQNjphBUqgBEOo149_bUjFMLOGsByT0LXqaBF3C-zN44ThrDeEdB5Q_bJsW5B/pub?gid=0&single=true&output=csv
```

The sheet key in this case is:
```
2PACX-1vSNjZItcRIaqBeN8xIBQNjphBUqgBEOo149_bUjFMLOGsByT0LXqaBF3C-zN44ThrDeEdB5Q_bJsW5B
```

So in my demo installation I can use the following link to have a JSON endpoint:

https://google-sheets.nico.dev/2PACX-1vSNjZItcRIaqBeN8xIBQNjphBUqgBEOo149_bUjFMLOGsByT0LXqaBF3C-zN44ThrDeEdB5Q_bJsW5B/?row=true

Feel free to play around with the params 😃

## Params
* `table={int}`: If the Spreadsheet has multiple tables you can pass the requested table
* `col=true`: Use the first Column of the Spreadsheet as a JSON/Array Key
* `row=true`: Use the first Row of the Spreadsheet as a JSON/Array Key
* `switch=true`: Switch Up the Array so it uses the Row as the first level array instead of the Col
* `nocache=true`: Bypass the cache (sets the cachetime to 0)
* `reverse=true`: Reverse the sort order of the entries
* `filter-col={col}|{filter},{col2}|{filter},..`: Add filter(s) to one ore multiple columns
* `filter-row={row}|{filter}|{filter2},{row2}|{filter},..`: Add filter(s) to one ore multiple rows

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
* `$sheet->reverseEntries(bool $boolean)`: Reverse the sort order of the entries
* `$sheet->setFilter(string $type = '{col|row}', string $key, array $filters)`: Set filter(s) to a rows or columns

### Filter
GoogleSheet Publish to JSON allows you to add filters to the values.

Let's say we have the following Table:

| Col1 | Col2 | Integer | Col4 | Col5 |
|---|---|---|---|---|
| Lorem | Ipsum | 1.5 | sit | amet |
| consetetur | sed | 2 | nonumy | eirmod |

Now we need to filter the "Integer"-Colum to make sure it only returns integers:

```php
$sheet->setFirstRowAsKey(true); // Sets the first row as JSON Key
$sheet->setFilter('col', 'integer', [ 'intval' ]); // filters the column with the key "integer" by using the "inval();" function
```

Endpoint that converts the title to a "key":

https://google-sheets.nico.dev/2PACX-1vSNjZItcRIaqBeN8xIBQNjphBUqgBEOo149_bUjFMLOGsByT0LXqaBF3C-zN44ThrDeEdB5Q_bJsW5B/?row=true&filter-col=title|key

#### Available filters

* md2html - converts markdown to html
* key - converts any string to lowercase, spaces to minus and removes non-alphanumeric characters
* [intval](https://www.php.net/manual/en/function.intval.php) - convert to an integer
* [floatval](https://www.php.net/manual/en/function.floatval.php) - convert to a float
* [boolval](https://www.php.net/manual/en/function.boolval.php) - convert to boolean
