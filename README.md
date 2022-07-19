Laravel Kendo UI DataSource
===========================

ESSENTIALLY ALL WORK ON THIS PROJECT WAS ORIGINALLY DONE BY USER meowcakes.  I HAVE FORKED THIS FROM ChemProf MERELY TO GIVE MYSELF CONTROL OVER THE DEPENDENCY VERSIONS.  I TAKE NO CREDIT OR RESPONSIBILITY FOR THE ORIGINAL SCRIPTS, OTHER THAN THE TRIVIAL ADJUSTMENTS I HAVE MADE.

Server side Kendo UI DataSource implementation for Laravel

### Version

Laravel 7 | Laravel 8 | Laravel 9
--------- | --------- | -----------
v1.*      | v1.*      | v1.*

### Installation

- [Laravel Kendo UI DataSource on Packagist](https://packagist.org/packages/ericli1018/laravel-kendo-ui-datasource)
- [Laravel Kendo UI DataSource on GitHub](https://github.com/ericli1018/laravel-kendo-ui-datasource)

Start by installing Laravel Kendo UI Datasoure if you have not done so already:

```bash
composer require ericli1018/laravel-kendo-ui-datasource
```

To get the latest version simply require it in your `composer.json` file.

~~~
"ericli1018/laravel-kendo-ui-datasource": "dev-main"
~~~

(Optional) You can register the facade in the `aliases` key of your `app/config/app.php` file.

~~~
'aliases' => array(
    'KendoDataSource' => 'Ericli1018\LaravelKendoUiDatasource\Facade',
)
~~~

### Basic Example

```php
$kendoUIDS = KendoDataSource::make(
	Input::all(),
	[
		// (Optional) specifying table, join table or table alias for query.
		// 'email' => ['string', 'join_table_name'],
		'id' => 'number',
		'name' => 'string',
		'created_at' => 'date',
		'fully_registered' => 'boolean',
	]
	// Option main table name for query
	// , 'main_table_name'
);
$query = (new App\Models\User())->newQuery();
$count = $kendoUIDS->execute($query);
// Option column name for count
// $count = $kendoUIDS->execute($query, 'column name');
return ['data' => $query->get()->toArray(), 'total' => $count];
```

### Example with Table Alias

```php
$kendoUIDS = KendoDataSource::make($request->all(),
	[
		'id' => ['number', 'm'],
		'email' => ['string'],
		'name' => 'string',
	],
	'm'
);
$query = (new App\Models\User())->newQuery()->from('users as m');
$count = $kendoUIDS->execute($query, '`m`.`id`');
return ['data' => $query->get()->toArray(), 'total' => $count];
```
