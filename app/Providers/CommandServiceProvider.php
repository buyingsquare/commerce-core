<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;


class CommandServiceProvider extends ServiceProvider {

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
        $ds = DIRECTORY_SEPARATOR;
        $basedir = dirname( dirname( __DIR__ ) ) . $ds;

        $this->publishes( [$basedir . 'config/shop.php' => config_path( 'shop.php' )], 'config' );
	}


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->mergeConfigFrom( dirname( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR . 'config/default.php', 'shop' );
	    
        $this->app->singleton( 'aimeos', function( $app ) {
            return new \App\Base\Aimeos( $app['config'] );
        });

        $this->app->singleton( 'aimeos.config', function( $app ) {
            return new \App\Base\Config( $app['config'], $app['aimeos'] );
        });

        $this->app->singleton( 'aimeos.i18n', function( $app ) {
            return new \App\Base\I18n( $this->app['config'], $app['aimeos'] );
        });

        $this->app->singleton( 'aimeos.locale', function( $app ) {
            return new \App\Base\Locale( $app['config'] );
        });

        $this->app->singleton( 'aimeos.context', function( $app ) {
            return new \App\Base\Context( $app['session.store'], $app['aimeos.config'], $app['aimeos.locale'], $app['aimeos.i18n'] );
        });

        $this->app->singleton( 'aimeos.support', function( $app ) {
            return new \App\Base\Support( $app['aimeos.context'], $app['aimeos.locale'] );
        });

        $this->app->singleton( 'aimeos.view', function( $app ) {
            return new \App\Base\View( $app['config'], $app['aimeos.i18n'], $app['aimeos.support'] );
        });

        $this->app->singleton( 'aimeos.shop', function( $app ) {
            return new \App\Base\Shop( $app['aimeos'], $app['aimeos.context'], $app['aimeos.view'] );
        });

        $this->commands( array(
            'App\Command\AccountCommand',
            'App\Command\CacheCommand',
            'App\Command\SetupCommand',
            'App\Command\JobsCommand',
        ) );
	}


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'App\Command\AccountCommand', 'App\Command\CacheCommand',
			'App\Command\SetupCommand', 'App\Command\JobsCommand',
		);
	}

}