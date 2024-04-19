<?php

namespace SpaanProductions\LaravelForm;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SpaanProductions\LaravelForm\HtmlBuilder
 */
class HtmlFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'html';
    }
}
