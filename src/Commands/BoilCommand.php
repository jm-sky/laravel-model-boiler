<?php

declare(strict_types=1);

namespace DevMadeIt\Boiler\Commands;

use DevMadeIt\Boiler\Exceptions\BoilerException;
use DevMadeIt\Boiler\Generator;
use DevMadeIt\Boiler\Schema\ModelSchemaCollection;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\search;

class BoilCommand extends Command implements PromptsForMissingInput
{
    protected Generator $generator;

    protected ModelSchemaCollection $columns;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boiler:all {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Boil models, types, resources';

    /**
     * Execute the console command.
     *
     * @throws BoilerException
     */
    public function handle()
    {
        $this->generator = new Generator();
        $this->generator->run($this->argument('model'));
    }

    protected function searchModelNames(string $search = ''): array
    {
        $path = app_path("Models/{$search}*.php");
        $files = collect(File::glob($path));
        $models = $files->map(fn (string $modelPath) => basename($modelPath, '.php'));

        return $models->toArray();
    }

    protected function promptForMissingArgumentsUsing()
    {
        return [
            'model' => fn () => search(
                label: 'Search for a model:',
                placeholder: 'E.g. User',
                options: fn ($value) => $this->searchModelNames($value)
            ),
        ];
    }
}
