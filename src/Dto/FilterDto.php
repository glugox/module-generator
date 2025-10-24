<?php

namespace Glugox\ModuleGenerator\Dto;

class FilterDto
{
    /**
     * @param array<int, string> $options
     */
    public function __construct(
        public readonly string $field,
        public readonly string $type,
        public readonly ?string $label = null,
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
            field: (string) ($data['field'] ?? ''),
            type: (string) ($data['type'] ?? ''),
            label: isset($data['label']) ? (string) $data['label'] : null,
            options: $options
        );
    }
}
