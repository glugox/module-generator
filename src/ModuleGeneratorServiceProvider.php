<?php

namespace Glugox\ModuleGenerator;

use Illuminate\Support\ServiceProvider;

class ModuleGeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('module-generator', function () {
            return new ModuleGeneratorManager();
        });
    }

    public function boot(): void
    {
        $this->publishes([
        __DIR__.'/../config/module-generator.php' => config_path('module-generator.php'),
    ], 'config');
    }
}
