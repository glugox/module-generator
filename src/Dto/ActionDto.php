<?php

namespace Glugox\ModuleGenerator\Dto;

class ActionDto
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $type = null,
        public readonly ?string $field = null
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            type: isset($data['type']) ? (string) $data['type'] : null,
            field: isset($data['field']) ? (string) $data['field'] : null
        );
    }
}
