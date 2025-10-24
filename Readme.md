# Glugox Module Generator

A module-first scaffolding engine for Laravel applications designed to power the upcoming Magic orchestrator. The package consumes a module specification and materialises the backend and frontend building blocks that Magic expects, keeping everything organised inside versioned modules. Think of it as the module analogue of [`glugox/magic`](https://github.com/glugox/magic): you describe the capabilities, entities, and UI actions that a module should expose, and the generator wires up the Laravel side for you.

## What the generator does today

- **Validates module specifications** before any files are written, including module metadata, entity schemas, relations, filters, and actions.
- **Maps specs into rich DTOs** that higher-level orchestration code (or tests) can reason about without touching raw arrays.
- **Creates module scaffolding** under `modules/<vendor>/<name>` including:
  - `composer.json` preconfigured for PSR-4 autoloading and Laravel service discovery.
  - `.manufacture-manifest.json` that the Magic orchestrator can ingest.
  - `src/Providers/ModuleServiceProvider.php` ready for custom bindings and bootstrapping.
- **Supports stub-driven output**, making it simple to tailor the generated files to your organisation’s conventions.

Future iterations will expand the writer pipeline to create Laravel UI, HTTP, database, and front-end artefacts so modules feel as complete as Magic’s runtime expects.

## Requirements

- PHP 8.2+
- Laravel (tested against the versions supported by `orchestra/testbench`)

## Installation

Require the package via Composer inside your Laravel project:

```bash
composer require glugox/module-generator
```

Publish the configuration to tweak paths, namespaces, and validation rules:

```bash
php artisan vendor:publish --provider="Glugox\ModuleGenerator\ModuleGeneratorServiceProvider" --tag=config
```

This will publish `config/module-generator.php` with sensible defaults for where modules live, their base namespace, and the stub directory.

## Configuration reference

Key options exposed in `config/module-generator.php`:

| Option | Description |
| --- | --- |
| `module_path` | Root directory where generated modules are written (defaults to `base_path('modules')`). |
| `namespace` | Default base namespace for generated modules. |
| `stubs_path` | Directory containing the stub templates used by writers. |
| `spec.rules` | Array of validation rules merged into the built-in schema rules. |

You can override these values per environment or publish your own stub files to align with your team’s standards.

## Crafting a module specification

The generator expects a structured array (or JSON document) describing the module. At a minimum you provide the schema version and module metadata; entities, relations, filters, and actions are optional but unlock richer scaffolding.

```json
{
  "schemaVersion": "1.0.0",
  "module": {
    "id": "glugox/module-billing",
    "name": "Billing",
    "namespace": "Glugox\\\\Billing",
    "description": "Invoices and payments",
    "capabilities": ["http:web", "http:api"]
  },
  "entities": [
    {
      "name": "Invoice",
      "fields": [
        { "name": "id", "type": "uuid", "hidden": true, "unique": true },
        { "name": "status", "type": "string", "searchable": true, "default": "draft", "options": ["draft", "paid", "void"] },
        { "name": "total", "type": "decimal", "nullable": false }
      ],
      "relations": [
        { "type": "belongsTo", "relatedEntityName": "Customer", "nullable": false },
        { "type": "hasMany", "relatedEntityName": "Payment", "cascade": true, "pivot": "invoice_payment" }
      ],
      "filters": [
        { "field": "status", "type": "select", "label": "Invoice Status", "options": ["draft", "paid", "void"] }
      ],
      "actions": [
        { "name": "markPaid", "type": "update", "field": "status" }
      ]
    }
  ]
}
```

### Fields, relations, filters, and actions

The DTO layer documents the shape of each component:

- **Fields** support flags for `searchable`, `unique`, `hidden`, `nullable`, an optional default value, and enumerated `options`.
- **Relations** describe how entities connect (e.g. `belongsTo`, `hasMany`), optionally specifying pivot tables, cascade behaviour, and nullability.
- **Filters** inform the orchestrator which UI filters to expose, including their type, label, and allowed options.
- **Actions** declare user-triggerable behaviours tied to a field or entity.

The validator enforces required keys and types, but you can extend or relax the schema by merging additional `spec.rules` in configuration.

## Generating a module

You can feed the specification to the facade, a service container binding, or directly to `ModuleGeneratorManager`:

```php
use Glugox\ModuleGenerator\Facades\ModuleGenerator;

$spec = json_decode(file_get_contents(base_path('specs/billing.json')), true);

ModuleGenerator::generate($spec);
```

This command will create a module directory under the configured `module_path` and populate it using the active writers. Each writer reads a stub, performs token replacement, and writes the finished file to disk.

The `.manufacture-manifest.json` file captures the canonical module metadata that Magic’s orchestrator will ingest to compose backend and frontend experiences. The generated service provider is ready for you to register bindings, routes, Livewire components, or Inertia pages that complete the module’s surface area.

## Customising writers and stubs

Writers are simple classes implementing `WriterInterface`. You can swap or extend them by passing a custom writer list when instantiating `ModuleGeneratorManager`, or by resolving the binding from the service container and decorating it. Each writer points to a stub file under the configured stub directory, so copying and editing those stubs lets you tailor the generated output without touching PHP code.

Future releases will ship additional writers for controllers, models, form requests, Vue/React components, and anything else Magic needs to assemble a full-stack module.

## Testing & development

Run the full quality suite locally with Pest, PHPStan, Pint, and Rector:

```bash
composer test
```

During package development you can also run targeted commands:

- `composer test:unit` – run unit tests with coverage.
- `composer lint` – format code with Pint and analyse types with PHPStan.
- `composer build` – refresh the Testbench workbench app.

These scripts align with the package skeleton provided by `orchestra/testbench` and keep the generator production-ready.

## Roadmap

- Expand the manifest schema to include UI layout, menu placement, and permissions.
- Generate Laravel backend artefacts (models, migrations, policies, controllers) and matching front-end components to mirror Magic’s runtime modules.
- Provide a CLI/Artisan wrapper so operators can scaffold modules directly from spec files stored in your Magic orchestrator.

Stay tuned as the project grows alongside the orchestrator—contributions and ideas are welcome!
