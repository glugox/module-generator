<?php

namespace Glugox\ModuleGenerator;

use Glugox\ModuleGenerator\Console\GenerateModuleCommand;
use Illuminate\Support\ServiceProvider;

class ModuleGeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleGeneratorManager::class, function () {
            return new ModuleGeneratorManager();
        });

        $this->app->alias(ModuleGeneratorManager::class, 'module-generator');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/module-generator.php' => config_path('module-generator.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateModuleCommand::class,
            ]);
        }
    }
}
