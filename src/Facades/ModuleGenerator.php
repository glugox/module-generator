<?php

namespace Glugox\ModuleGenerator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Glugox\ModuleGenerator\ModuleGeneratorManager
 *
 * @method static mixed generate(array $spec)
 */
class ModuleGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'module-generator';
    }
}
