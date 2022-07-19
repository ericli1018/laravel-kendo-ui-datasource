<?php
namespace Ericli1018\LaravelKendoUiDatasource;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return DataSourceManager::class; }

}
