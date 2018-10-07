<?php

namespace Studio\Novacron\Tests;

use Studio\Totem\Totem;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\NovaServiceProvider;
use Collective\Html\HtmlServiceProvider;
use Studio\Novacron\ToolServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Studio\Totem\Providers\TotemServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();

        $this->artisan('nova:install');

        $this->artisan('migrate', ['--database' => 'testing']);

        $this->artisan('totem:assets');

        $this->loadLaravelMigrations(['--database' => 'testing']);

        $this->withFactories(__DIR__.'/../database/factories/');

        $auth = function () {
            switch (app()->environment()) {
                case 'local':
                    return true;

                    break;
                case 'testing':
                    return Auth::check();

                    break;
                default:
                    return false;
            }
        };

        Totem::auth($auth);

        Route::middlewareGroup('nova', []);
    }

    protected function getPackageAliases($app)
    {
        return [
            'Form' => FormFacade::class,
            'Html' => HtmlFacade::class,
        ];
    }

    protected function getPackageProviders($app)
    {
        return [
            NovaServiceProvider::class,
            TotemServiceProvider::class,
            HtmlServiceProvider::class,
            ToolServiceProvider::class,
        ];
    }
}
