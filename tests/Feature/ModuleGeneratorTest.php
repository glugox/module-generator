<?php


use Glugox\ModuleGenerator\Facades\ModuleGenerator;
use Glugox\ModuleGenerator\ModuleGeneratorManager;
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
        'models' => [],
    ];

    $specFile = $specPath.'/billing.json';
    writeSpec($specFile, $spec);

    $result = ModuleGenerator::generate($spec);

    // Check the files were created correctly
    $modulePath = base_path('modules/glugox/module-billing');
    expect(File::exists($modulePath . '/composer.json'))->toBeTrue()
        ->and(File::exists($modulePath . '/.manufacture-manifest.json'))->toBeTrue()
        ->and(File::exists($modulePath . '/src/Providers/ModuleServiceProvider.php'))->toBeTrue();

});


/**
 * Helper to write a spec file.
 */
function writeSpec(string $path, array $spec): void
{
    File::ensureDirectoryExists(dirname($path));
    file_put_contents($path, json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}