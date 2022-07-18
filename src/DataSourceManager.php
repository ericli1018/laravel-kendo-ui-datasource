<?php namespace LaravelKendoUiDatasource;

use \Illuminate\Foundation\Application;

class DataSourceManager
{

	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function make(array $input, array $columns, $table_name = null)
	{
		return new DataSource($this->app, $input, $columns, $table_name);
	}

}
