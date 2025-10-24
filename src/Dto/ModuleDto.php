<?php

namespace Glugox\ModuleGenerator\Dto;

class ModuleDto
{
    /**
     * @param array<int, EntityDto> $entities
     */
    public function __construct(
        public readonly string $schemaVersion,
        public readonly ModuleMetadataDto $module,
        public readonly array $entities = []
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $entities = [];
        if (isset($data['entities']) && is_array($data['entities'])) {
            $entities = array_map(static fn (array $entity): EntityDto => EntityDto::fromArray($entity), $data['entities']);
        }

        $moduleData = isset($data['module']) && is_array($data['module']) ? $data['module'] : [];

        return new self(
            schemaVersion: (string) ($data['schemaVersion'] ?? ''),
            module: ModuleMetadataDto::fromArray($moduleData),
            entities: $entities
        );
    }
}
