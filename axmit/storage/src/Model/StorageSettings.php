<?php

namespace Axmit\Storage\Model;

use Model;

/**
 * Class StorageSettings
 *
 * @package Axmit\Storage\Model
 */
class StorageSettings extends Model
{
    /**
     * @var array
     */
    public $implement = ['System.Behaviors.SettingsModel'];

    /**
     * @var string
     */
    public $settingsCode = 'axmit_storage_settings';

    /**
     * @var string
     */
    public $settingsFields = 'fields.yaml';
}
