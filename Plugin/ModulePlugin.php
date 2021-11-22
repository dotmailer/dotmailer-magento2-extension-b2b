<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\Email\Model\Connector\Module;

class ModulePlugin
{
    const MODULE_NAME = 'Dotdigitalgroup_B2b';
    const MODULE_DESCRIPTION = 'Dotdigital for Magento B2B';

    /**
     * @var Module
     */
    private $module;

    /**
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    /**
     * @param Module $module
     * @param array $modules
     * @return array
     */
    public function beforeFetchActiveModules(Module $module, array $modules = [])
    {
        $modules[] = [
            'name' => self::MODULE_DESCRIPTION,
            'version' => $this->module->getModuleVersion(self::MODULE_NAME)
        ];
        return [
            $modules
        ];
    }
}
