<?php

namespace Tests;


use Orchestra\Testbench\TestCase as Orchestra;
use Glugox\ModuleGenerator\ModuleGeneratorServiceProvider;

/**
 * @property string $specPath
 */
class TestCase extends Orchestra
{

    /**
     * @property string $specPath
     */
    public string $specPath = __DIR__ . '/specs';

    protected function getPackageProviders($app)
    {
        return [
            ModuleGeneratorServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // If you need to override config
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));

        // Set base path for the application
        $app->setBasePath(__DIR__ );
    }
}