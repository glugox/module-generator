<?php

namespace Glugox\ModuleGenerator;

class SpecValidator
{
    /**
     * @var array<string, mixed>
     */
    private array $rules;

    public function __construct(?array $rules = null)
    {
        $configRules = [];
        if (function_exists('config')) {
            $configuredRules = config('module-generator.spec.rules', []);
            if (is_array($configuredRules)) {
                $configRules = $configuredRules;
            }
        }

        $this->rules = $rules ?? $this->defaultRules();
        if ($configRules !== []) {
            $this->rules = array_replace_recursive($this->rules, $configRules);
        }
    }

    /**
     * Validate the module specification.
     *
     * @param array<string, mixed> $spec The module specification to validate.
     * @throws \InvalidArgumentException if the specification is invalid.
     */
    public function validate(array $spec): void
    {
        $this->validateNode($spec, $this->rules, 'spec');
    }

    /**
     * @param mixed                $value
     * @param array<string, mixed> $rule
     */
    private function validateNode(mixed $value, array $rule, string $path): void
    {
        $nullable = (bool) ($rule['nullable'] ?? false);

        if ($value === null) {
            if ($nullable) {
                return;
            }

            throw new \InvalidArgumentException(sprintf('%s is required.', $path));
        }

        $type = $rule['type'] ?? null;

        if (is_string($type)) {
            $this->assertType($value, $type, $path);
        }

        $this->applyConstraints($value, $rule, $path);

        if (isset($rule['items']) && is_array($rule['items']) && is_array($value)) {
            foreach ($value as $index => $item) {
                $this->validateNode($item, $rule['items'], sprintf('%s[%s]', $path, $index));
            }
        }

        if (!is_array($value)) {
            return;
        }

        if (isset($rule['required']) && is_array($rule['required'])) {
            foreach ($rule['required'] as $key => $childRule) {
                if (!array_key_exists($key, $value)) {
                    throw new \InvalidArgumentException(sprintf('%s.%s is required.', $path, $key));
                }

                $this->validateNode($value[$key], $childRule, sprintf('%s.%s', $path, $key));
            }
        }

        if (isset($rule['optional']) && is_array($rule['optional'])) {
            foreach ($rule['optional'] as $key => $childRule) {
                if (!array_key_exists($key, $value)) {
                    continue;
                }

                $this->validateNode($value[$key], $childRule, sprintf('%s.%s', $path, $key));
            }
        }
    }

    private function assertType(mixed $value, string $expected, string $path): void
    {
        $isValid = match ($expected) {
            'string' => is_string($value),
            'bool', 'boolean' => is_bool($value),
            'array' => is_array($value),
            'object' => is_array($value),
            'mixed' => true,
            default => true,
        };

        if (!$isValid) {
            throw new \InvalidArgumentException(sprintf('%s must be of type %s.', $path, $expected));
        }
    }

    /**
     * @param array<string, mixed> $rule
     */
    private function applyConstraints(mixed $value, array $rule, string $path): void
    {
        if (isset($rule['enum']) && is_array($rule['enum']) && !in_array($value, $rule['enum'], true)) {
            throw new \InvalidArgumentException(sprintf('%s must be one of [%s].', $path, implode(', ', $rule['enum'])));
        }

        if (isset($rule['format']) && is_string($rule['format'])) {
            $this->validateFormat($rule['format'], $value, $path);
        }
    }

    private function validateFormat(string $format, mixed $value, string $path): void
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException(sprintf('%s must be a string to validate format.', $path));
        }

        if ($format === 'module_id' && !str_contains($value, '/')) {
            throw new \InvalidArgumentException(sprintf('%s must be a vendor/name string.', $path));
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultRules(): array
    {
        return [
            'type' => 'object',
            'required' => [
                'schemaVersion' => ['type' => 'string'],
                'module' => [
                    'type' => 'object',
                    'required' => [
                        'id' => ['type' => 'string', 'format' => 'module_id'],
                        'name' => ['type' => 'string'],
                        'namespace' => ['type' => 'string'],
                    ],
                    'optional' => [
                        'description' => ['type' => 'string'],
                        'capabilities' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
            'optional' => [
                'entities' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => [
                            'name' => ['type' => 'string'],
                        ],
                        'optional' => [
                            'fields' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'required' => [
                                        'name' => ['type' => 'string'],
                                        'type' => ['type' => 'string'],
                                    ],
                                    'optional' => [
                                        'searchable' => ['type' => 'bool'],
                                        'unique' => ['type' => 'bool'],
                                        'hidden' => ['type' => 'bool'],
                                        'nullable' => ['type' => 'bool'],
                                        'default' => ['type' => 'mixed', 'nullable' => true],
                                        'options' => [
                                            'type' => 'array',
                                            'items' => ['type' => 'string'],
                                        ],
                                    ],
                                ],
                            ],
                            'relations' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'required' => [
                                        'type' => ['type' => 'string'],
                                        'relatedEntityName' => ['type' => 'string'],
                                    ],
                                    'optional' => [
                                        'pivot' => ['type' => 'string', 'nullable' => true],
                                        'cascade' => ['type' => 'bool'],
                                        'nullable' => ['type' => 'bool'],
                                    ],
                                ],
                            ],
                            'filters' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'required' => [
                                        'field' => ['type' => 'string'],
                                        'type' => ['type' => 'string'],
                                    ],
                                    'optional' => [
                                        'label' => ['type' => 'string', 'nullable' => true],
                                        'options' => [
                                            'type' => 'array',
                                            'items' => ['type' => 'string'],
                                        ],
                                    ],
                                ],
                            ],
                            'actions' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'required' => [
                                        'name' => ['type' => 'string'],
                                    ],
                                    'optional' => [
                                        'type' => ['type' => 'string', 'nullable' => true],
                                        'field' => ['type' => 'string', 'nullable' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}