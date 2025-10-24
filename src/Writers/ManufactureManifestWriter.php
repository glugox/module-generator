<?php

namespace Glugox\ModuleGenerator\Writers;

class ManufactureManifestWriter extends AbstractStubWriter
{
    protected function stubPath(): string
    {
        return $this->stubDirectory() . '/module/manifest.stub';
    }

    protected function targetPath(array $spec, string $modulePath): string
    {
        return $modulePath . '/.manufacture-manifest.json';
    }

    protected function replacements(array $spec, string $modulePath): array
    {
        $module = $spec['module'];

        return [
            '{{generated_at}}' => $this->generatedAt(),
            '{{module_id}}' => $module['id'],
            '{{module_name}}' => $module['name'],
            '{{module_description}}' => $module['description'] ?? '',
            '{{module_capabilities}}' => json_encode($module['capabilities'] ?? []),
            '{{schema_version}}' => $spec['schemaVersion'],
        ];
    }
}
