<?php

namespace Dotdigitalgroup\B2b\Model\SharedCatalog;

/**
 * Class Name
 * Service class to hold the name of the shared catalog during script execution.
 * See SharedCatalogBuilderPlugin, SharedCatalogUpdatePlugin
 */
class Name
{
    /**
     * @var string
     */
    private $sharedCatalogName;

    /**
     * @param $name
     */
    public function setSharedCatalogName($name)
    {
        $this->sharedCatalogName = $name;
    }

    /**
     * @return string
     */
    public function getSharedCatalogName()
    {
        return $this->sharedCatalogName;
    }
}
