<?php

namespace DevMadeIt\Boiler\Generators;

use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;

class BaseGenerator
{
    use InteractsWithIO;

    public function __construct(
    ) {
        $this->output = new ConsoleOutput();
    }
}
