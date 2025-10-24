
# Glugox Package Skeleton for Laravel
Laravel Package Skeleton

## Features
- Sample feature 1

## Installation
To install the Package Skeleton, you can use Composer. Run the following command in your terminal

```bash
composer glugox/module-generator
```

## Configuration
After installing the package, you need to publish the configuration file. Run the following command:
```bash
php artisan vendor:publish --provider="Glugox\ModuleGenerator\ModuleGeneratorServiceProvider"
```

This will create a `module-generator.php` configuration file in your `config` directory. You can customize the settings according to your requirements.
## Usage
To start tracking changes in your Laravel packages, you can use the provided Artisan commands. Here are
some examples:
 - To run service;
```bash
ModuleGenerator::service($parameter);
```

## Future Plans
There will be new features added in the future.