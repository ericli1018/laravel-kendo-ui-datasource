Laravel Kendo UI DataSource
===========================

ESSENTIALLY ALL WORK ON THIS PROJECT WAS ORIGINALLY DONE BY USER meowcakes.  I HAVE FORKED THIS FROM ChemProf MERELY TO GIVE MYSELF CONTROL OVER THE DEPENDENCY VERSIONS.  I TAKE NO CREDIT OR RESPONSIBILITY FOR THE ORIGINAL SCRIPTS, OTHER THAN THE TRIVIAL ADJUSTMENTS I HAVE MADE.

Server side Kendo UI DataSource implementation for Laravel

### Installation

- [Laravel Kendo UI DataSource on Packagist](https://packagist.org/packages/ericli1018/laravel-kendo-ui-datasource)
- [Laravel Kendo UI DataSource on GitHub](https://github.com/ericli1018/laravel-kendo-ui-datasource)

To get the latest version simply require it in your `composer.json` file.

~~~
"ericli1018/laravel-kendo-ui-datasource": "dev-main"
~~~

You can register the facade in the `aliases` key of your `app/config/app.php` file.

~~~
'aliases' => array(

    'KendoDataSource' => 'Ericli1018\LaravelKendoUiDatasource\Facade',

)
~~~

### Example

```php
$kendoUIDS = KendoDataSource::make(
	Input::all(),
	[
		// Option for specifying join table 
		// 'address' => ['string', 'join_table_name'],
		'suburb' => 'string',
		'phone' => 'string',
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
