<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core;

use Goomento\PageBuilder\Core\Base\Module;
use Goomento\PageBuilder\Core\Base\Module as BaseModule;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class ModulesManager
 * @package Goomento\PageBuilder\Core
 */
class ModulesManager
{

    /**
     * Registered modules.
     *
     * Holds the list of all the registered modules.
     *
     *
     * @var array
     */
    private $modules = [];

    /**
     * Modules manager constructor.
     *
     * Initializing the SagoTheme modules manager.
     *
     */
    public function __construct()
    {
        foreach ($this->getModulesNames() as $module_name) {
            $active = !!call_user_func($module_name . '::isActive');
            if ($active) {
                /** @var BaseModule $module */
                $module = StaticObjectManager::get($module_name);
                $this->modules[ $module->getName() ] = $module;
            }
        }
    }

    /**
     * @return string[]
     */
    public function getModulesNames()
    {
        return [
            History\Module::class,
            DynamicTags\Module::class,
        ];
    }

    /**
     * Get modules.
     *
     * Retrieve all the registered modules or a specific module.
     *
     *
     * @param string $module_name Module name.
     *
     * @return null|Module|Module[] All the registered modules or a specific module.
     * @deprecated
     */
    public function getModules($module_name)
    {
        if ($module_name) {
            if (isset($this->modules[ $module_name ])) {
                return $this->modules[ $module_name ];
            }

            return null;
        }

        return $this->modules;
    }
}
