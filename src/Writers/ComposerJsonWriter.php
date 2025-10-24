<?php

namespace Glugox\ModuleGenerator\Writers;

class ComposerJsonWriter extends AbstractStubWriter
{
    protected function stubPath(): string
    {
        return $this->stubDirectory() . '/module/composer.stub';
    }

    protected function targetPath(array $spec, string $modulePath): string
    {
        return $modulePath . '/composer.json';
    }

    protected function replacements(array $spec, string $modulePath): array
    {
        $module = $spec['module'];
        $namespace = rtrim($module['namespace'], '\\');

        return [
            '{{generated_at}}' => $this->generatedAt(),
            '{{module_id}}' => $module['id'],
            '{{module_description}}' => $module['description'] ?? '',
            '{{module_namespace_psr4}}' => $namespace . '\\',
            '{{module_provider_class}}' => $namespace . '\\Providers\\ModuleServiceProvider',
        ];
    }
}
