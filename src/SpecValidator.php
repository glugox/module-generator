<?php

namespace Glugox\ModuleGenerator;

class SpecValidator
{
    /**
     * Validate the module specification.
     *
     * @param array $spec The module specification to validate.
     * @throws \InvalidArgumentException if the specification is invalid.
     */
    public function validate(array $spec): void
    {
        // Basic validation of the spec structure
        // schemaVersion
        if (!isset($spec['schemaVersion']) || !is_string($spec['schemaVersion'])) {
            throw new \InvalidArgumentException('Missing or invalid schemaVersion');
        }

        // module
        if (!isset($spec['module']) || !is_array($spec['module'])) {
            throw new \InvalidArgumentException('Missing or invalid module section');
        }

        // Validate module details
        $module = $spec['module'];

        // id
        if (!isset($module['id']) || !is_string($module['id']) || !str_contains($module['id'], '/')) {
            throw new \InvalidArgumentException('Module id must be a vendor/name string.');
        }

        // name
        if (!isset($module['name']) || !is_string($module['name'])) {
            throw new \InvalidArgumentException('Module name is required.');
        }

        // namespace
        if (!isset($module['namespace']) || !is_string($module['namespace'])) {
            throw new \InvalidArgumentException('Module namespace is required.');
        }

        // models
        if (isset($spec['models'])) {

            // Ensure models is an array
            if (!is_array($spec['models'])) {
                throw new \InvalidArgumentException('Models must be an array.');
            }

            // Validate each model
            foreach ($spec['models'] as $index => $model) {
                // Ensure model is an array
                if (!is_array($model)) {
                    throw new \InvalidArgumentException("Model entry at index {$index} must be an object.");
                }

                // Validate model details
                // name
                if (!isset($model['name']) || !is_string($model['name'])) {
                    throw new \InvalidArgumentException("models[{$index}].name is required.");
                }

                // table
                if (!isset($model['table']) || !is_string($model['table'])) {
                    throw new \InvalidArgumentException("models[{$index}].table is required.");
                }

                // fields
                if (isset($model['fields'])) {

                    // Ensure fields is an array
                    if (!is_array($model['fields'])) {
                        throw new \InvalidArgumentException("models[{$index}].fields must be an array.");
                    }

                    // Validate each field
                    foreach ($model['fields'] as $fieldIndex => $field) {
                        // Ensure field is an array
                        if (!is_array($field)) {
                            throw new \InvalidArgumentException("models[{$index}].fields[{$fieldIndex}] must be an object.");
                        }

                        // name
                        if (!isset($field['name']) || !is_string($field['name'])) {
                            throw new \InvalidArgumentException("models[{$index}].fields[{$fieldIndex}].name is required.");
                        }

                        // type
                        if (!isset($field['type']) || !is_string($field['type'])) {
                            throw new \InvalidArgumentException("models[{$index}].fields[{$fieldIndex}].type is required.");
                        }
                    }
                }
            }
        }
    }
}