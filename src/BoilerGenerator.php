<?php

declare(strict_types=1);

namespace DevMadeIt\Boiler;

use DevMadeIt\Boiler\Generators\BaseGenerator;
use DevMadeIt\Boiler\Generators\ResourceGenerator;
use DevMadeIt\Boiler\Schema\DbSchemaLoader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ReflectionClass;

class BoilerGenerator extends BaseGenerator
{
    protected ReflectionClass $reflection;

    protected Model $model;

    protected DbSchemaLoader $schemaLoader;

    protected Collection $schema;

    public function __construct(
        protected string $modelClassName,
    ) {
        parent::__construct();
        $this->reflection = new ReflectionClass($this->modelClassName);
        $this->model = $this->reflection->newInstance();
        $this->schemaLoader = new DbSchemaLoader($this->model);
    }

    public function loadSchema(): void
    {
        $this->schema = $this->schemaLoader->loadSchema();
    }

    public function generate(): void
    {
        $this->info('BoilerGenerator - generate');

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
        $resourceGenerator = new ResourceGenerator($this->modelClassName, schema: $this->schema);
        $resourceGenerator->generateResource();
        $resourceGenerator->generateResourceCollection();
    }
}
