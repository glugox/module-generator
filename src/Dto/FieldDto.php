<?php

namespace Glugox\ModuleGenerator\Dto;

class FieldDto
{
    /**
     * @param array<int, string> $options
     */
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly bool $searchable = false,
        public readonly bool $unique = false,
        public readonly bool $hidden = false,
        public readonly bool $nullable = false,
        public readonly mixed $default = null,
        public readonly array $options = []
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $options = [];
        if (isset($data['options']) && is_array($data['options'])) {
            $options = array_map(static fn ($value): string => (string) $value, $data['options']);
        }

        return new self(
            name: (string) ($data['name'] ?? ''),
            type: (string) ($data['type'] ?? ''),
            searchable: (bool) ($data['searchable'] ?? false),
            unique: (bool) ($data['unique'] ?? false),
            hidden: (bool) ($data['hidden'] ?? false),
            nullable: (bool) ($data['nullable'] ?? false),
            default: $data['default'] ?? null,
            options: $options
        );
    }
}
