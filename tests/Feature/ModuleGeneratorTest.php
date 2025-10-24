<?php


use Glugox\ModuleGenerator\Dto\ActionDto;
use Glugox\ModuleGenerator\Dto\EntityDto;
use Glugox\ModuleGenerator\Dto\FieldDto;
use Glugox\ModuleGenerator\Dto\FilterDto;
use Glugox\ModuleGenerator\Dto\ModuleSpecMapper;
use Glugox\ModuleGenerator\Dto\RelationDto;
use Glugox\ModuleGenerator\Facades\ModuleGenerator;
use Illuminate\Support\Facades\File;

$specPath = '';

beforeEach(function () {
    File::ensureDirectoryExists(base_path('.tmp'));
});

afterEach(function () {
    //File::deleteDirectory(base_path('.tmp'));
});


it('generates composer metadata and a manifest', function () {
    $specPath = base_path('.tmp/specs');
    $spec = [
        'schemaVersion' => '1.0.0',
        'module' => [
            'id' => 'glugox/module-billing',
            'name' => 'Billing',
            'namespace' => 'Glugox\\Billing',
            'description' => 'Invoices and payments',
            'capabilities' => ['http:web', 'http:api'],
        ],
        'entities' => [
            [
                'name' => 'Invoice',
                'fields' => [
                    [
                        'name' => 'id',
                        'type' => 'uuid',
                        'hidden' => true,
                        'unique' => true,
                    ],
                    [
                        'name' => 'status',
                        'type' => 'string',
                        'searchable' => true,
                        'default' => 'draft',
                        'options' => ['draft', 'paid', 'void'],
                    ],
                    [
                        'name' => 'total',
                        'type' => 'decimal',
                        'nullable' => false,
                    ],
                ],
                'relations' => [
                    [
                        'type' => 'belongsTo',
                        'relatedEntityName' => 'Customer',
                        'nullable' => false,
                    ],
                    [
                        'type' => 'hasMany',
                        'relatedEntityName' => 'Payment',
                        'cascade' => true,
                        'pivot' => 'invoice_payment',
                    ],
                ],
                'filters' => [
                    [
                        'field' => 'status',
                        'type' => 'select',
                        'label' => 'Invoice Status',
                        'options' => ['draft', 'paid', 'void'],
                    ],
                ],
                'actions' => [
                    [
                        'name' => 'markPaid',
                        'type' => 'update',
                        'field' => 'status',
                    ],
                ],
            ],
            [
                'name' => 'Payment',
                'fields' => [
                    [
                        'name' => 'id',
                        'type' => 'uuid',
                    ],
                    [
                        'name' => 'invoice_id',
                        'type' => 'uuid',
                        'nullable' => false,
                    ],
                    [
                        'name' => 'amount',
                        'type' => 'decimal',
                    ],
                ],
                'relations' => [
                    [
                        'type' => 'belongsTo',
                        'relatedEntityName' => 'Invoice',
                        'nullable' => false,
                    ],
                ],
                'filters' => [
                    [
                        'field' => 'created_at',
                        'type' => 'date',
                        'label' => 'Created At',
                    ],
                ],
                'actions' => [
                    [
                        'name' => 'refund',
                        'type' => 'mutation',
                        'field' => 'status',
                    ],
                ],
            ],
        ],
    ];

    $specFile = $specPath.'/billing.json';
    writeSpec($specFile, $spec);

    $result = ModuleGenerator::generate($spec);
    expect($result)->toBeTrue();

    // Check the files were created correctly
    $modulePath = base_path('modules/glugox/module-billing');
    expect(File::exists($modulePath . '/composer.json'))->toBeTrue()
        ->and(File::exists($modulePath . '/.manufacture-manifest.json'))->toBeTrue()
        ->and(File::exists($modulePath . '/src/Providers/ModuleServiceProvider.php'))->toBeTrue();

    $moduleDto = (new ModuleSpecMapper())->map($spec);

    expect($moduleDto->schemaVersion)->toBe('1.0.0')
        ->and($moduleDto->module->id)->toBe('glugox/module-billing')
        ->and($moduleDto->module->extra['capabilities'])->toBe(['http:web', 'http:api']);

    /** @var array<int, EntityDto> $entities */
    $entities = $moduleDto->entities;
    expect($entities)->toHaveCount(2)
        ->and($entities[0])->toBeInstanceOf(EntityDto::class)
        ->and($entities[1])->toBeInstanceOf(EntityDto::class);

    $invoice = $entities[0];
    expect($invoice->name)->toBe('Invoice')
        ->and($invoice->fields)->toHaveCount(3)
        ->and($invoice->fields[0])->toBeInstanceOf(FieldDto::class)
        ->and($invoice->fields[0]->hidden)->toBeTrue()
        ->and($invoice->fields[0]->unique)->toBeTrue()
        ->and($invoice->fields[1]->searchable)->toBeTrue()
        ->and($invoice->fields[1]->options)->toBe(['draft', 'paid', 'void'])
        ->and($invoice->relations)->toHaveCount(2)
        ->and($invoice->relations[0])->toBeInstanceOf(RelationDto::class)
        ->and($invoice->relations[0]->type)->toBe('belongsTo')
        ->and($invoice->relations[1]->pivot)->toBe('invoice_payment')
        ->and($invoice->relations[1]->cascade)->toBeTrue()
        ->and($invoice->filters)->toHaveCount(1)
        ->and($invoice->filters[0])->toBeInstanceOf(FilterDto::class)
        ->and($invoice->filters[0]->options)->toBe(['draft', 'paid', 'void'])
        ->and($invoice->actions)->toHaveCount(1)
        ->and($invoice->actions[0])->toBeInstanceOf(ActionDto::class)
        ->and($invoice->actions[0]->field)->toBe('status');

    $payment = $entities[1];
    expect($payment->fields)->toHaveCount(3)
        ->and($payment->relations)->toHaveCount(1)
        ->and($payment->filters[0]->label)->toBe('Created At')
        ->and($payment->actions[0]->name)->toBe('refund');

});


/**
 * Helper to write a spec file.
 */
function writeSpec(string $path, array $spec): void
{
    File::ensureDirectoryExists(dirname($path));
    file_put_contents($path, json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}