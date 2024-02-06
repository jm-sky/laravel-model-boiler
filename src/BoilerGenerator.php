<?php

declare(strict_types=1);

namespace DevMadeIt\Boiler;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use DevMadeIt\Boiler\Schema\DbSchemaLoader;
use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;

class BoilerGenerator
{
    use InteractsWithIO;

    protected ReflectionClass $reflection;

    protected Model $model;

    protected DbSchemaLoader $schemaLoader;

    public function __construct(
        protected string $modelClassName,
    ) {
        $this->reflection = new ReflectionClass($this->modelClassName);
        $this->model = $this->reflection->newInstance();
        $this->schemaLoader = new DbSchemaLoader($this->model);
        $this->output = new ConsoleOutput();
    }

    public function loadSchema(): void
    {
        $this->schemaLoader->loadSchema();
    }

    public function generate(): void
    {
        $this->info("BoilerGenerator - generate");

        $this->generateAnnotations();
        $this->generateTypeScript();
        $this->generateResources();
    }

    public function generateAnnotations(): void
    {
    }

    public function generateTypeScript(): void
    {
    }

    public function generateResources(): void
    {
        $resourceName = Str::of($this->modelClassName)->afterLast('\\') . 'Resource';
        $collectionName = Str::of($this->modelClassName)->afterLast('\\') . 'Collection';

        $this->info("Resources");
        $this->info("- resource: {$resourceName}");
        $this->info("- collection: {$collectionName}");

        Artisan::call('make:resource', ['name' => $resourceName]);
        Artisan::call('make:resource', ['name' => $collectionName]);
    }
}
