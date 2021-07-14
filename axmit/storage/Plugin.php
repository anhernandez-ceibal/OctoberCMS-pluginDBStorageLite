<?php namespace Axmit\Storage;

use Axmit\Storage\Model\StorageSettings;
use Illuminate\Support\Facades\App;
use System\Classes\PluginBase;

/**
 * Storage Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Storage',
            'description' => 'Provides adapter to store CMS content into DB',
            'author'      => 'Axmit',
            'icon'        => 'icon-database',
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        App::register(StorageServiceProvider::class);
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'axmit.storage.settings_manage' => [
                'tab'   => 'Axmit Storage',
                'label' => 'Manage data storage permissions',
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Storage Settings',
                'description' => 'Manage CMS content storage settings.',
                'category'    => 'system::lang.system.categories.cms',
                'icon'        => 'icon-database',
                'class'       => StorageSettings::class,
                'order'       => 500,
                'keywords'    => 'storage settings database db',
                'permissions' => ['axmit.storage.settings_manage']
            ]
        ];
    }

}
