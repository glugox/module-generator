<?php

namespace Glugox\ModuleGenerator\Dto;

class EntityDto
{
    /**
     * @param array<int, FieldDto>   $fields
     * @param array<int, RelationDto> $relations
     * @param array<int, FilterDto>   $filters
     * @param array<int, ActionDto>   $actions
     */
    public function __construct(
        public readonly string $name,
        public readonly array $fields = [],
        public readonly array $relations = [],
        public readonly array $filters = [],
        public readonly array $actions = []
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $fields = [];
        if (isset($data['fields']) && is_array($data['fields'])) {
            $fields = array_map(static fn (array $field): FieldDto => FieldDto::fromArray($field), $data['fields']);
        }

        $relations = [];
        if (isset($data['relations']) && is_array($data['relations'])) {
            $relations = array_map(static fn (array $relation): RelationDto => RelationDto::fromArray($relation), $data['relations']);
        }

        $filters = [];
        if (isset($data['filters']) && is_array($data['filters'])) {
            $filters = array_map(static fn (array $filter): FilterDto => FilterDto::fromArray($filter), $data['filters']);
        }

        $actions = [];
        if (isset($data['actions']) && is_array($data['actions'])) {
            $actions = array_map(static fn (array $action): ActionDto => ActionDto::fromArray($action), $data['actions']);
        }

        return new self(
            name: (string) ($data['name'] ?? ''),
            fields: $fields,
            relations: $relations,
            filters: $filters,
            actions: $actions
        );
    }
}
