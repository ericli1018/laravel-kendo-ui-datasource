<?php
namespace Ericli1018\LaravelKendoUiDatasource;

use Illuminate\Support\ServiceProvider;

class LaravelKendoUiDatasourceServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	public function boot()
	{
		
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['laravel-kendo-ui-datasource'] = $this->app->singleton(DataSourceManager::class, function($container) {
			return new DataSourceManager($container);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
