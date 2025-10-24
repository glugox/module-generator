<?php

namespace Glugox\ModuleGenerator\Writers;

class ModuleServiceProviderWriter extends AbstractStubWriter
{
    protected function stubPath(): string
    {
        return $this->stubDirectory() . '/module/module_service_provider.stub';
    }

    protected function targetPath(array $spec, string $modulePath): string
    {
        return $modulePath . '/src/Providers/ModuleServiceProvider.php';
    }

    protected function replacements(array $spec, string $modulePath): array
    {
        $module = $spec['module'];

        return [
            '{{generated_at}}' => $this->generatedAt(),
            '{{module_namespace}}' => rtrim($module['namespace'], '\\'),
        ];
    }
}
