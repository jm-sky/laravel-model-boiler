<?php

namespace DevMadeIt\Boiler\Generators;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ResourceGenerator extends BaseGenerator
{
    public function __construct(
        public string $modelClassName,
        public Collection $schema,
    ) {
        parent::__construct();
    }

    public function generateResource(): void
    {
        $name = Str::of($this->modelClassName)->afterLast('\\');
        $resourceName = "{$name}Resource";
        $this->info("- resource: {$resourceName}");
        Artisan::call('make:resource', ['name' => $resourceName]);

        $name = $this->qualifyClass($resourceName);
        $path = $this->getPath($name);
        File::put($path, $this->buildClass($name));
    }

    public function generateResourceCollection(): void
    {
        $collectionName = Str::of($this->modelClassName)->afterLast('\\').'Collection';
        $this->info("- collection: {$collectionName}");
        Artisan::call('make:resource', ['name' => $collectionName]);
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace.'\Http\Resources';
    }

    protected function qualifyClass(string $name): string
    {
        $name = ltrim($name, '\\/');
        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    protected function getPath(string $name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return app_path().'/'.str_replace('\\', '/', $name).'.php';
    }

    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    protected function rootNamespace(): string
    {
        return App::getNamespace();
    }

    protected function buildClass($name)
    {
        $stub = File::get($this->getStub());

        return $this->replaceNamespace($stub, $name)
            ->replaceProperties($stub, $name)
            ->replaceClass($stub, $name);
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $searches = [
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel'],
            ['{{ namespace }}', '{{ rootNamespace }}', '{{ namespacedUserModel }}'],
            ['{{namespace}}', '{{rootNamespace}}', '{{namespacedUserModel}}'],
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$this->getNamespace($name), $this->rootNamespace()],
                $stub
            );
        }

        return $this;
    }

    protected function replaceProperties(&$stub, $name)
    {
        $indent = '            ';
        $annotations = $this->schema->map(function ($column): string {
            $columnName = $column['column']->getName();
            $camelName = Str::of($columnName)->camel()->toString();

            return "* @property mixed \${$camelName}";
        })->join("\n ");

        $properties = $this->schema->map(function ($column): string {
            $columnName = $column['column']->getName();
            $camelName = Str::of($columnName)->camel()->toString();

            return "'{$camelName}' => \$this->{$columnName},";
        })->join("\n{$indent}");

        $stub = str_replace(['{{ annotations }}', '{{annotations}}'], $annotations, $stub);
        $stub = str_replace(['{{ properties }}', '{{properties}}'], $properties, $stub);

        return $this;
    }

    protected function getStub(): string
    {
        return __DIR__.'/../stubs/resource.stub';
    }

    protected function replaceClass($stub, $name): string
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
    }
}
