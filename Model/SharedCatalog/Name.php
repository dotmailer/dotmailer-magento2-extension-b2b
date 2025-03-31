<?php

namespace Dotdigitalgroup\B2b\Model\SharedCatalog;

/**
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
     * Set the shared catalog name.
     *
     * @param string $name
     */
    public function setSharedCatalogName($name)
    {
        $this->sharedCatalogName = $name;
    }

    /**
     * Get the shared catalog name.
     *
     * @return string
     */
    public function getSharedCatalogName()
    {
        return $this->sharedCatalogName;
    }
}
