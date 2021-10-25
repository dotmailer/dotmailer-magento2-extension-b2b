<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\Email\Model\Connector\Module;
use Magento\Framework\Module\ModuleListInterface;

class ModulePlugin
{
    const MODULE_NAME = 'Dotdigitalgroup_B2b';
    const MODULE_DESCRIPTION = 'Dotdigital for Magento B2B';

    /**
     * @var ModuleListInterface
     */
    private $fullModuleList;

    /**
     * @param ModuleListInterface $moduleListInterface
     */
    public function __construct(ModuleListInterface $moduleListInterface)
    {
        $this->fullModuleList = $moduleListInterface;
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
            'version' => $this->fullModuleList->getOne(self::MODULE_NAME)['setup_version']
        ];
        return [
            $modules
        ];
    }
}
