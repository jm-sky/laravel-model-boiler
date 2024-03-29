<?php

declare(strict_types=1);

namespace DevMadeIt\Boiler;

use DevMadeIt\Boiler\Commands\BoilCommand;
use Illuminate\Support\Facades\Config;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BoilerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('boiler')
            ->hasConfigFile('boiler')
            ->hasCommand(BoilCommand::class);
    }

    public static function forceConfigSet(): void
    {
        if (! config('boiler')) {
            Config::set('boiler', include (__DIR__.'/../config/boiler.php'));
        }
    }
}
