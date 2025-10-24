<?php

use Glugox\ModuleGenerator\Dto\ModuleDto;
use Glugox\ModuleGenerator\Dto\ModuleSpecMapper;

it('maps an entities specification into typed DTOs', function () {
    $spec = [
        'schemaVersion' => '1.0',
        'module' => [
            'id' => 'Glugox/ModuleGenerator',
            'name' => 'Module Generator',
            'namespace' => 'Glugox\\ModuleGenerator',
        ],
        'entities' => [
            [
                'name' => 'User',
                'fields' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'name', 'type' => 'string', 'searchable' => true],
                    ['name' => 'email', 'type' => 'string', 'unique' => true, 'searchable' => true],
                    ['name' => 'password', 'type' => 'password', 'hidden' => true],
                    ['name' => 'is_admin', 'type' => 'boolean', 'default' => false],
                ],
                'relations' => [
                    ['type' => 'belongsToMany', 'relatedEntityName' => 'Role', 'pivot' => 'role_user'],
                ],
                'filters' => [
                    ['field' => 'name', 'type' => 'text'],
                    ['field' => 'email', 'type' => 'text'],
                    ['field' => 'is_admin', 'type' => 'boolean'],
                ],
                'actions' => [
                    ['name' => 'assign_role'],
                    ['name' => 'remove_role'],
                ],
            ],
        ],
    ];

    $mapper = new ModuleSpecMapper();
    $dto = $mapper->map($spec);

    expect($dto)->toBeInstanceOf(ModuleDto::class)
        ->and($dto->schemaVersion)->toBe('1.0')
        ->and($dto->module->id)->toBe('Glugox/ModuleGenerator')
        ->and($dto->module->name)->toBe('Module Generator')
        ->and($dto->module->namespace)->toBe('Glugox\\ModuleGenerator')
        ->and($dto->entities)->toHaveCount(1);

    $userEntity = $dto->entities[0];

    expect($userEntity->name)->toBe('User')
        ->and($userEntity->fields)->toHaveCount(5)
        ->and($userEntity->relations)->toHaveCount(1)
        ->and($userEntity->filters)->toHaveCount(3)
        ->and($userEntity->actions)->toHaveCount(2);

    $emailField = $userEntity->fields[2];

    expect($emailField->unique)->toBeTrue()
        ->and($emailField->searchable)->toBeTrue()
        ->and($emailField->nullable)->toBeFalse();

    $relation = $userEntity->relations[0];
    expect($relation->type)->toBe('belongsToMany')
        ->and($relation->relatedEntityName)->toBe('Role')
        ->and($relation->pivot)->toBe('role_user');

    $action = $userEntity->actions[0];
    expect($action->name)->toBe('assign_role');
});
