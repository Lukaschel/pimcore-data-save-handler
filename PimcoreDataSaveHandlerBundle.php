<?php
/**
 * PimcoreDataSaveHandlerBundle
 * Copyright (c) Lukaschel
 */

namespace Lukaschel\PimcoreDataSaveHandlerBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class PimcoreDataSaveHandlerBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    const PACKAGE_NAME = 'lukaschel/pimcore-data-save-handler';

    /**
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * @return array|\Pimcore\Routing\RouteReferenceInterface[]|string[]
     */
    public function getJsPaths()
    {
        return [
            // '/bundles/pimcoredatasavehandler/js/pimcore/startup.js'
        ];
    }

    /**
     * @return string
     */
    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }
}
