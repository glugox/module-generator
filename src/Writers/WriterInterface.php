<?php

namespace Glugox\ModuleGenerator\Writers;

interface WriterInterface
{
    /**
     * Write the generated file for the given module specification.
     */
    public function write(array $spec, string $modulePath): void;
}
