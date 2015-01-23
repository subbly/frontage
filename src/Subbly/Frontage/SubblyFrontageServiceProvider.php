<?php 

namespace Subbly\Frontage;

use Illuminate\Support\ServiceProvider;

class SubblyFrontageServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('subbly/frontage');

    include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		/*
		 * Register the service provider for the dependency.
		 */
		$this->app->register('Barryvdh\Debugbar\ServiceProvider');

		/*
		 * Create aliases for the dependency.
		 */
		$loader = \Illuminate\Foundation\AliasLoader::getInstance();
		$loader->alias('Debugbar', 'Barryvdh\Debugbar\Facade');
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
