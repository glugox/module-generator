<?php

namespace Glugox\ModuleGenerator;

use Glugox\ModuleGenerator\Writers\ComposerJsonWriter;
use Glugox\ModuleGenerator\Writers\ManufactureManifestWriter;
use Glugox\ModuleGenerator\Writers\ModuleServiceProviderWriter;
use Glugox\ModuleGenerator\Writers\WriterInterface;
use Illuminate\Support\Facades\File;

/**
 * Coordinates module scaffolding from a specification.
 */
class ModuleGeneratorManager
{
    /**
     * @var array<int, WriterInterface>
     */
    private array $writers;

    public function __construct(
        private readonly SpecValidator $validator = new SpecValidator(),
        ?array $writers = null
    ) {
        $this->writers = $writers ?? [
            new ComposerJsonWriter(),
            new ManufactureManifestWriter(),
            new ModuleServiceProviderWriter(),
        ];
    }

    /**
     * Generate a new module.
     */
    public function generate(array $spec): bool
    {
        $this->validator->validate($spec);

        $module = $spec['module'];
        $moduleId = $module['id'];
        [$vendor, $name] = explode('/', $moduleId, 2);
        $modulePath = base_path('modules/' . $vendor . '/' . $name);

        File::ensureDirectoryExists($modulePath);

        foreach ($this->writers as $writer) {
            $writer->write($spec, $modulePath);
        }

        return true;
    }
}
