<?php

namespace Glugox\ModuleGenerator\Dto;

class ModuleMetadataDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $namespace,
        public readonly ?string $description = null,
        public readonly ?string $version = null,
        /**
         * @var array<string, mixed>
         */
        public readonly array $extra = []
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $extra = $data;

        $id = (string) ($extra['id'] ?? '');
        $name = (string) ($extra['name'] ?? '');
        $namespace = (string) ($extra['namespace'] ?? '');
        $description = isset($extra['description']) ? (string) $extra['description'] : null;
        $version = isset($extra['version']) ? (string) $extra['version'] : null;

        unset($extra['id'], $extra['name'], $extra['namespace'], $extra['description'], $extra['version']);

        return new self(
            id: $id,
            name: $name,
            namespace: $namespace,
            description: $description,
            version: $version,
            extra: $extra
        );
    }
}
