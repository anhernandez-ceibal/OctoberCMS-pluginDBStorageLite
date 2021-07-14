<?php

namespace Axmit\Storage\Datasource;

use Axmit\Storage\Model\CmsContentModel;
use October\Rain\Halcyon\Datasource\Datasource;
use October\Rain\Halcyon\Datasource\DatasourceInterface;
use October\Rain\Halcyon\Processors\Processor;
use October\Rain\Halcyon\Processors\SectionParser;

/**
 * Class DbDatasource
 *
 * @package Axmit\Storage\Datasource
 */
class DbDatasource extends Datasource implements DatasourceInterface
{
    /**
     * DbDatasource constructor.
     *
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        $this->postProcessor = $processor;
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
        $object = $this->find($dirName, $fileName, $extension);

        if (!$object) {
            return null;
        }

        return [
            'fileName' => $object->filename . '.' . $object->extension,
            'content'  => $object->content,
            'mtime'    => $this->unixTimestamp($object->updated_at),
        ];
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
        $options = array_filter(
            array_merge(
                [
                    'columns'    => null,  // Only return specific columns (fileName, mtime, content)
                    'extensions' => null,  // Match specified extensions
                    // 'fileMatch'  => null,  // could not be applied
                    'orders'     => null,  // @todo
                    'limit'      => null,
                    'offset'     => null,
                ],
                $options)
        );

        $columns          = ['*'];
        $mandatoryColumns = [
            'filename',
            'extension',
            'settings',
            'created_at',
            'updated_at',
        ];
        $columnsMap       = [
            'fileName' => 'filename',
            'dirName'  => 'directory',
        ];

        if (isset($options['columns']) && is_array($options['columns']) && $options['columns'] != ['*']) {
            $columns = array_map(
                function ($el) use ($columnsMap) {
                    return isset($columnsMap[$el]) ? $columnsMap[$el] : $el;
                },
                $options['columns']
            );
            $columns = array_unique(array_merge($columns, $mandatoryColumns));
        }

        $query = CmsContentModel::query()->where('directory', $dirName);

        if (isset($options['extensions'])) {
            $query->whereIn('extension', (array)$options['extensions']);
        }

        if (isset($options['limit'])) {
            $query->limit($options['limit']);
        }

        if (isset($options['offset'])) {
            $query->skip($options['offset']);
        }

        $result = [];

        foreach ($query->get($columns) as $object) {
            array_push(
                $result,
                [
                    'fileName' => $object->filename . '.' . $object->extension,
                    'content'  => $object->content ?: $object->settings,
                    'mtime'    => $this->unixTimestamp($object->updated_at),
                ]
            );
        }

        return $result;
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
        $object            = new CmsContentModel();
        $object->directory = $dirName;
        $object->filename  = $fileName;
        $object->extension = $extension;
        $object->content   = $content;

        $parts            = SectionParser::parse($content);
        $object->settings = isset($parts['settings']) ? SectionParser::render(['settings' => $parts['settings']]) : '';

        try {
            return $object->save();
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }

    /**
     * Updates an existing template.
     *
     * @param  string $dirName
     * @param  string $fileName
     * @param  string $extension
     * @param  array  $content
     *
     * @return int
     */
    public function update($dirName, $fileName, $extension, $content, $oldFileName = null, $oldExtension = null)
    {
        $object = $this->find($dirName, $oldFileName ?: $fileName, $oldExtension ?: $extension);

        if (!$object) {
            return $this->insert($dirName, $fileName, $extension, $content);
        }

        $object->filename  = $fileName;
        $object->extension = $extension;
        $object->content   = $content;

        $parts            = SectionParser::parse($content);
        $object->settings = isset($parts['settings']) ? SectionParser::render(['settings' => $parts['settings']]) : '';

        try {
            return $object->save();
        } catch (\Throwable $ex) {
            throw $ex;
        }
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
        $object = $this->find($dirName, $fileName, $extension);

        if (!$object) {
            return true;
        }

        return $object->delete();
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
        $object = $this->find($dirName, $fileName, $extension);

        return $object ? $this->unixTimestamp($object->updated_at) : null;
    }

    /**
     * @param  string $dirName
     * @param  string $fileName
     * @param  string $extension
     *
     * @return CmsObjectModel|null
     */
    protected function find($dirName, $fileName, $extension)
    {
        return CmsContentModel::query()
                              ->where('directory', $dirName)
                              ->where('filename', $fileName)
                              ->where('extension', $extension)
                              ->first();
    }

    /**
     * @param \DateTime $date
     *
     * @return int
     */
    protected function unixTimestamp($date)
    {
        return $date instanceof \DateTime ? $date->getTimestamp() : null;
    }

    public function getPathsCacheKey(){}
    public function getAvailablePaths(){}

}
