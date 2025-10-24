<?php

namespace Glugox\ModuleGenerator\Dto;

class RelationDto
{
    public function __construct(
        public readonly string $type,
        public readonly string $relatedEntityName,
        public readonly ?string $pivot = null,
        public readonly bool $cascade = false,
        public readonly bool $nullable = false
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: (string) ($data['type'] ?? ''),
            relatedEntityName: (string) ($data['relatedEntityName'] ?? ''),
            pivot: isset($data['pivot']) ? (string) $data['pivot'] : null,
            cascade: (bool) ($data['cascade'] ?? false),
            nullable: (bool) ($data['nullable'] ?? false)
        );
    }
}
