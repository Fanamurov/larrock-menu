<?php

namespace Larrock\ComponentMenu\Facades;

use Illuminate\Support\Facades\Facade;

class LarrockMenu extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'larrockmenu';
    }

}