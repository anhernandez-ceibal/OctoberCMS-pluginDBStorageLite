<?php

namespace Axmit\Storage\Datasource;

use October\Rain\Halcyon\Datasource\Datasource;
use October\Rain\Halcyon\Datasource\DatasourceInterface;

/**
 * Class DatasourceProxy
 *
 * @package Axmit\Storage\Datasource
 */
class DatasourceProxy extends Datasource implements DatasourceInterface
{

    /**
     * @var DbDatasource
     */
    protected $dbDatasource;

    /**
     * @var DatasourceInterface
     */
    protected $fallbackDatasource;

    /**
     * @var array
     */
    protected $directories = [];

    /**
     * @var boolean
     */
    protected $enabled = false;

    /**
     * DatasourceProxy constructor.
     *
     * @param DbDatasource $dbDatasource
     * @param array        $directories
     * @param bool         $enabled
     */
    public function __construct(DbDatasource $dbDatasource, array $directories, $enabled = false)
    {
        $this->dbDatasource = $dbDatasource;
        $this->directories  = $directories;
        $this->enabled      = $enabled;
    }

    /**
     * @param DatasourceInterface $fallbackDatasource
     */
    public function setFallbackDatasource(DatasourceInterface $fallbackDatasource)
    {
        $this->fallbackDatasource = $fallbackDatasource;
    }

    /**
     * @return \October\Rain\Halcyon\Processors\Processor
     */
    public function getPostProcessor()
    {
        return $this->dbDatasource->getPostProcessor();
    }

    /**
     * Returns a single template.
     *
     * @param  string $dirName
     * @param  string $fileName
     * @param  string $extension
     *
     * @return mixed
     */
    public function selectOne($dirName, $fileName, $extension)
    {
        return $this->proxy($dirName, 'selectOne', func_get_args());
    }

    /**
     * Returns all templates.
     *
     * @param  string $dirName
     * @param  array  $options
     *
     * @return array
     */
    public function select($dirName, array $options = [])
    {
        return $this->proxy($dirName, 'select', func_get_args());
    }

    /**
     * Creates a new template.
     *
     * @param  string $dirName
     * @param  string $fileName
     * @param  string $extension
     * @param  array  $content
     *
     * @return bool
     */
    public function insert($dirName, $fileName, $extension, $content)
    {
        return $this->proxy($dirName, 'insert', func_get_args());
    }

    /**
     * Updates an existing template.
     *
     * @param  string $dirName
     * @param  string $fileName
     * @param  string $extension
     * @param  array  $content
     * @param  array  $oldFileName 
     * @param  array  $oldExtension 
     *
     * @return int
     */
    public function update($dirName, $fileName, $extension, $content, $oldFileName = NULL, $oldExtension = NULL)
    {
        return $this->proxy($dirName, 'update', func_get_args());
    }

    /**
     * Run a delete statement against the datasource.
     *
     * @param  string $dirName
     * @param  string $fileName
     * @param  string $extension
     *
     * @return int
     */
    public function delete($dirName, $fileName, $extension)
    {
        return $this->proxy($dirName, 'delete', func_get_args());
    }

    /**
     * Return the last modified date of an object
     *
     * @param  string $dirName
     * @param  string $fileName
     * @param  string $extension
     *
     * @return int
     */
    public function lastModified($dirName, $fileName, $extension)
    {
        return $this->proxy($dirName, 'lastModified', func_get_args());
    }

    /**
     * @param string $dirName
     *
     * @return bool
     */
    protected function isFallback($dirName)
    {
        if (false === $this->enabled) {
            return true;
        }

        return in_array($dirName, $this->directories) ? false : true;
    }

    /**
     * @param string $dirName
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    protected function proxy($dirName, $method, array $arguments)
    {
        if (!$this->fallbackDatasource instanceof DatasourceInterface) {
            throw new \RuntimeException(
                'Fallback data source is not defined'
            );
        }

        $isFallback = $this->isFallback($dirName);
        $source     = $isFallback ? $this->fallbackDatasource : $this->dbDatasource;

        return call_user_func_array([$source, $method], $arguments);
    }

    public function getPathsCacheKey(){}
    public function getAvailablePaths(){}

}
