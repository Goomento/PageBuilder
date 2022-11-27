<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder;

use Magento\Framework\App\ObjectManager;

const DEBUGGING = false;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class Developer
{
    const DEBUG = 'debug';

    const VERSION = 'version';

    const DESCRIPTION = 'description';

    const HOMEPAGE = 'homepage';

    const MAGENTO_VERSION = 'magento_version';

    /**
     * @var array|null
     */
    private static $variables;

    /**
     * Get variable within Goomento
     *
     * @param string $name
     * @return void
     */
    public static function getVar(string $name)
    {
        self::loadVariable();
        return self::$variables[$name] ?? null;
    }

    /**
     * Set variable within Goomento
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function setVar(string $name, $value)
    {
        self::loadVariable();
        self::$variables[$name] = $value;
    }

    /**
     * Load all variables
     *
     * @return void
     */
    private static function loadVariable()
    {
        if (self::$variables === null) {
            self::$variables = [];
            // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            $composer = file_get_contents(__dir__ . '/composer.json');
            $composer = json_decode($composer);
            self::$variables[self::VERSION] = $composer->version;
            self::$variables[self::DESCRIPTION] = $composer->description;
            self::$variables[self::HOMEPAGE] = $composer->homepage;
            self::$variables[self::DEBUG] = DEBUGGING;
            $magentoVersion = ObjectManager::getInstance()->get(\Magento\Framework\App\ProductMetadataInterface::class);
            self::$variables[self::MAGENTO_VERSION] = $magentoVersion->getVersion();
        }
    }
}
