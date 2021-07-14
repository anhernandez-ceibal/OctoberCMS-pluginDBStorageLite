<?php

namespace Axmit\Storage;

use Axmit\Storage\Datasource\DatasourceProxy;
use Axmit\Storage\Datasource\DbDatasource;
use Axmit\Storage\Model\StorageSettings;
use Axmit\Storage\Resolver\Resolver;
use October\Rain\Halcyon\Processors\Processor;
use October\Rain\Support\ServiceProvider;

/**
 * Class StorageServiceProvider
 */
class StorageServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'axmit.storage.datasource.proxy',
            function () {
                $enabled     = (bool)StorageSettings::get('enabled', false);
                $directories = StorageSettings::get('directories', []);
                $directories = !is_array($directories) ? [] : $directories;
                
                return new DatasourceProxy(
                    new DbDatasource(new Processor()), $directories, $enabled
                );
            }
        );

        $this->app->extend(
            'halcyon',
            function () {
                return new Resolver();
            }
        );
    }
}
