<?php

namespace Axmit\Storage\Resolver;

use Axmit\Storage\Datasource\DatasourceProxy;
use Illuminate\Support\Facades\App;
use October\Rain\Halcyon\Datasource\DatasourceInterface;
use October\Rain\Halcyon\Datasource\Resolver as BaseResolver;

/**
 * Class Resolver
 *
 * @package Axmit\Storage\Resolver
 */
class Resolver extends BaseResolver
{
    public function addDatasource($name, DatasourceInterface $datasource)
    {
        /** @var DatasourceProxy $proxy */
        $proxy = App::make('axmit.storage.datasource.proxy');
        $proxy->setFallbackDatasource($datasource);

        parent::addDatasource($name, $proxy);
    }

}
