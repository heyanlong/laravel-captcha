<?php namespace Heyanlong\Captcha;

use Illuminate\Support\ServiceProvider;

/**
 * Class CaptchaServiceProvider
 * @package Heyanlong\Captcha
 */
class CaptchaServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return null
     */
    public function boot()
    {
        // Publish configuration files
        $this->publishes([
            __DIR__ . '/../config/captcha.php' => config_path('captcha.php')
        ], 'config');
        // HTTP routing
        if (starts_with($this->app->version(), '5.2.') !== false) {
            //Laravel 5.2.x
            $this->app['router']->get('captcha/{config?}', '\Heyanlong\Captcha\CaptchaController@getCaptcha')->middleware('web');
        } else {
            //Laravel 5.0.x ~ 5.1.x
            $this->app['router']->get('captcha/{config?}', '\Heyanlong\Captcha\CaptchaController@getCaptcha');
        }
        // Validator extensions
        $this->app['validator']->extend('captcha', function ($attribute, $value, $parameters) {
            $config = 'default';
            if (isset($parameters[0])) {
                $config = $parameters[0];
            }
            return captcha_check($value, $config);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Merge configs
        $this->mergeConfigFrom(
            __DIR__ . '/../config/captcha.php', 'captcha'
        );
        // Bind captcha
        $this->app->bind('captcha', function ($app) {
            return new Captcha();
        });
    }
}