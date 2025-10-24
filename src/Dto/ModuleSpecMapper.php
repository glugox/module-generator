<?php

namespace Glugox\ModuleGenerator\Dto;

class ModuleSpecMapper
{
    /**
     * @param array<string, mixed> $spec
     */
    public function map(array $spec): ModuleDto
    {
        return ModuleDto::fromArray($spec);
    }
}
