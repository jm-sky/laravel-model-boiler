<?php

declare(strict_types=1);

namespace DevMadeIt\Boiler;

use DevMadeIt\Boiler\Exceptions\BoilerException;
use DevMadeIt\Boiler\Generators\BaseGenerator;
use DevMadeIt\Boiler\Generators\TypescriptGenerator;
use DevMadeIt\Boiler\Schema\ModelSchemaCollection;
use DevMadeIt\Boiler\Schema\SchemaLoader;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Generator extends BaseGenerator
{
    public string $model;

    public string $modelClassName;

    protected SchemaLoader $schemaLoader;

    protected ModelSchemaCollection $columns;

    protected string $modelsNamespace = '\\App\\Models\\';

    protected bool $generateTypescript = true;

    public function __construct()
    {
        parent::__construct();
        BoilerServiceProvider::forceConfigSet();

        $this->modelsNamespace = config('boiler.models_namespace', "{$this->rootNamespace()}\\Models\\");
        $this->generateTypescript = config('boiler.ts.generate', $this->generateTypescript);
    }

    public function run(string $model): void
    {
        $this->initSchema($model);

        if ($this->generateTypescript) {
            (new TypescriptGenerator($this->model, $this->columns))->run();
        }

        $boilerGenerator = new BoilerGenerator($this->modelClassName);
        $boilerGenerator->loadSchema();
        $boilerGenerator->generate();
    }

    /**
     * @throws BoilerException
     */
    protected function initSchema(string $model): void
    {
        $this->model = Str::of($model)->studly()->toString();
        $this->modelClassName = Str::contains($this->model, '\\') ? $this->model : "{$this->modelsNamespace}{$this->model}";

        if (! class_exists($this->modelClassName)) {
            throw new BoilerException("No model found '{$this->modelClassName}'");
        }

        $this->info("Boiling '{$this->model}'");
        $this->info("- using '{$this->modelClassName}' model");

        $this->schemaLoader = new SchemaLoader($this->modelClassName);
        $this->columns = $this->schemaLoader->getColumns();
    }

    protected function rootNamespace(): string
    {
        return App::getNamespace();
    }
}
