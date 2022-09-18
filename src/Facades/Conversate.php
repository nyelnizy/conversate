<?php

namespace Amot\Conversate\Facades;

use Illuminate\Support\Facades\Facade;

class Conversate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'conversate';
    }
}
