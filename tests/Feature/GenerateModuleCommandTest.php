<?php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

it('generates a module from a JSON spec via the console command', function () {
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

    $specFile = base_path('.tmp/specs/billing.json');
    File::ensureDirectoryExists(dirname($specFile));
    file_put_contents($specFile, json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    $this->artisan('module:generate', ['spec' => $specFile])
        ->expectsOutput('Module [glugox/module-billing] generated successfully.')
        ->assertExitCode(Command::SUCCESS);

    $modulePath = base_path('modules/glugox/module-billing');

    expect(File::exists($modulePath . '/composer.json'))->toBeTrue()
        ->and(File::exists($modulePath . '/.manufacture-manifest.json'))->toBeTrue()
        ->and(File::exists($modulePath . '/src/Providers/ModuleServiceProvider.php'))->toBeTrue();
})->finally(function () {
    File::deleteDirectory(base_path('modules'));
    File::deleteDirectory(base_path('.tmp'));
});
