<?php

namespace Glugox\ModuleGenerator\Console;

use Glugox\ModuleGenerator\ModuleGeneratorManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use JsonException;
use Throwable;

class GenerateModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:generate {spec : Path to the module specification JSON file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a module from a JSON specification file.';

    public function handle(ModuleGeneratorManager $generator): int
    {
        $specPath = $this->argument('spec');

        if (! is_string($specPath) || $specPath === '') {
            $this->error('The spec path must be a non-empty string.');

            return self::FAILURE;
        }

        if (! File::exists($specPath)) {
            $this->error(sprintf('Spec file not found at path [%s].', $specPath));

            return self::FAILURE;
        }

        try {
            $spec = json_decode(File::get($specPath), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->error(sprintf('Unable to decode JSON spec: %s', $exception->getMessage()));

            return self::FAILURE;
        }

        if (! is_array($spec)) {
            $this->error('The decoded spec must resolve to an associative array.');

            return self::FAILURE;
        }

        try {
            $generator->generate($spec);
        } catch (Throwable $exception) {
            $this->error(sprintf('Failed to generate module: %s', $exception->getMessage()));

            return self::FAILURE;
        }

        $moduleId = $spec['module']['id'] ?? null;

        if (is_string($moduleId) && $moduleId !== '') {
            $this->info(sprintf('Module [%s] generated successfully.', $moduleId));
        } else {
            $this->info('Module generated successfully.');
        }

        return self::SUCCESS;
    }
}
