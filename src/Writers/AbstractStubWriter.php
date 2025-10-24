<?php

namespace Glugox\ModuleGenerator\Writers;

use Illuminate\Support\Facades\File;

abstract class AbstractStubWriter implements WriterInterface
{
    public function write(array $spec, string $modulePath): void
    {
        $stub = File::get($this->stubPath());
        $replacements = $this->replacements($spec, $modulePath);
        $content = str_replace(array_keys($replacements), array_values($replacements), $stub);

        $targetPath = $this->targetPath($spec, $modulePath);
        File::ensureDirectoryExists(dirname($targetPath));
        File::put($targetPath, $content);
    }

    /**
     * Get the path to the stub file.
     */
    abstract protected function stubPath(): string;

    /**
     * Get the path to the target file.
     */
    abstract protected function targetPath(array $spec, string $modulePath): string;

    /**
     * Get the replacement map for the stub placeholders.
     *
     * @return array<string, string>
     */
    abstract protected function replacements(array $spec, string $modulePath): array;

    protected function generatedAt(): string
    {
        return gmdate('c');
    }

    protected function stubDirectory(): string
    {
        return dirname(__DIR__, 1) . '/../stubs';
    }
}
