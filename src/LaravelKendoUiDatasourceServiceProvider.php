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
		$this->offerPublishing();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
            __DIR__ . '/config/config.php',
            'laravel-kendo-ui-datasource'
        );

		$this->app->singleton(DataSourceManager::class, function($app) {
			return new DataSourceManager($app);
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

	/**
	 * Offer publishing.
	 *
	 * @return null
	 */
	protected function offerPublishing() {
		if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

		$this->publishes([
            __DIR__ . '/config/config.php' => config_path('laravel-kendo-ui-datasource.php'),
        ], 'config');
	}
}
