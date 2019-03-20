<?php
/**
 * PimcoreDataSaveHandlerBundle
 * Copyright (c) Lukaschel
 */

namespace Lukaschel\PimcoreDataSaveHandlerBundle\EventListener;

use Doctrine\DBAL\Migrations\AbortMigrationException;
use Lukaschel\PimcoreDataSaveHandlerBundle\Configuration\Configuration;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\File;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;

class DataObjectPreUpdateListener
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $config;

    /**
     * DataObjectPreAddListener constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->config = $this->configuration->getConfig('custom_save_handling');
    }

    /**
     * @param DataObjectEvent $event
     *
     * @throws AbortMigrationException
     */
    public function onPreUpdate(DataObjectEvent $event)
    {
        $dataObject = $event->getObject();
        $dataObjectClass = get_class($dataObject);

        if (empty($this->config)) {
            return;
        }

        if (!array_key_exists($dataObjectClass, $this->config)) {
            return;
        }

        if ($this->config[$dataObjectClass]['key']) {
            $dataObjectKey = $this->buildPatternString($this->config[$dataObjectClass]['key'], $dataObject);
            $dataObject->setKey(File::getValidFilename($dataObjectKey));
        }

        if ($this->config[$dataObjectClass]['path']) {
            $dataObjectPath = $this->buildPatternString($this->config[$dataObjectClass]['path'], $dataObject);
            if ($folderId = $this->buildObjectFolder($dataObjectPath)) {
                $dataObject->setParentId($folderId);
            }
        }
    }

    /**
     * @param string         $string
     * @param AbstractObject $dataObject
     *
     * @return mixed
     */
    private function buildPatternString($string, $dataObject)
    {
        if (!preg_match('#(\{.*?\})#', $string)) {
            return $string;
        }

        $scheme_array = [];
        preg_match_all('#\{(.*?)\}#', $string, $schemeMatch);
        foreach ($schemeMatch[1] as $scheme) {
            if (preg_match('#\.#', $scheme)) {
                $subScheme = explode('.', $scheme);
                $parentObject = '';
                for ($count = 0; $count < sizeof($subScheme); ++$count) {
                    $methodName = 'get' . ucfirst($subScheme[$count]);
                    if ($count == 0 and method_exists($dataObject, $methodName)) {
                        $parentObject = $dataObject->$methodName();
                    }
                    if (is_object($parentObject)) {
                        if (method_exists($parentObject, $methodName)) {
                            $scheme_array['{' . $scheme . '}'] = File::getValidFilename($parentObject->$methodName());
                        }
                    }
                }
            } else {
                $methodName = 'get' . ucfirst($scheme);
                if (method_exists($dataObject, $methodName)) {
                    $scheme_array['{' . $scheme . '}'] = File::getValidFilename($dataObject->$methodName());
                }
            }
        }

        return strtr($string, $scheme_array);
    }

    /**
     * @param string $path
     *
     * @throws AbortMigrationException
     *
     * @return bool|int
     */
    private function buildObjectFolder($path)
    {
        if (!$path) {
            return false;
        }

        $path = ltrim(rtrim($path, '/'), '/');
        $dataObjectPath = DataObject\Folder::getByPath('/' . $path . '/');
        if ($dataObjectPath instanceof DataObject\Folder) {
            return $dataObjectPath->getId();
        }

        try {
            $lastObjectFolder = DataObject\Service::createFolderByPath('/' . $path, ['locked' => true]);
            $dataObjectFolderId = $lastObjectFolder->getId();
        } catch (\Exception $e) {
            throw new AbortMigrationException(sprintf('Failed to create data object storage. error was: "%s"', $e->getMessage()));
        }

        return $dataObjectFolderId;
    }
}
