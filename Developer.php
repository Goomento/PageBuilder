<?php /** @noinspection ALL */
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;

const DEBUGGING = false;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
// phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
class Developer
{
    const DEBUG = 'debug';

    const VERSION = 'version';

    const DESCRIPTION = 'description';

    const HOMEPAGE = 'homepage';

    const MAGENTO_VERSION = 'magento_version';
    /**
     * Default Breakpoints
     * In case of has the custom breakpoints, modify this and SCSS breakpoint also
     */
    public const DEFAULT_BREAKPOINTS = [
        'xs' => 0,
        'sm' => 480,
        'md' => 768,
        'lg' => 1025,
        'xl' => 1440,
        'xxl' => 1600,
    ];

    /**
     * Bridge between variable and data
     *
     * @var array
     */
    private static $variableGenerator = [
        self::MAGENTO_VERSION => 'getMagentoVersion' /** @see self::getMagentoVersion */
    ];

    /**
     * @var array|null
     */
    private static $variables;

    /**
     * Get variable within Goomento
     *
     * @param string $name
     * @return mixed
     */
    public static function getVar(string $name)
    {
        self::initVariables();
        if (!isset(self::$variables[$name]) && isset(self::$variableGenerator[$name])) {
            self::$variables[$name] = self::{self::$variableGenerator[$name]}();
        }
        return self::$variables[$name] ?? null;
    }

    /**
     * Get current version
     *
     * @return string
     */
    public static function version()
    {
        return (string) Developer::getVar(Developer::VERSION);
    }

    /**
     * Is Debugging
     * Set some resources as develop version (Eg: remove `.min` from URL ...)
     *
     * @return bool
     */
    public static function debug()
    {
        return (bool) Developer::getVar(Developer::DEBUG);
    }

    /**
     * Get Current Magento Version
     * @return string
     */
    public static function magentoVersion()
    {
        return (string) Developer::getVar(Developer::MAGENTO_VERSION);
    }

    /**
     * @return string
     */
    private static function getMagentoVersion()
    {
        $magentoVersion = ObjectManager::getInstance()->get(ProductMetadataInterface::class);
        return $magentoVersion->getVersion();
    }

    /**
     * Set variable within Goomento
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function setVar(string $name, $value)
    {
        self::initVariables();
        self::$variables[$name] = $value;
    }

    /**
     * Load all variables
     *
     * @return void
     */
    private static function initVariables()
    {
        if (self::$variables === null) {
            self::$variables = [];
            $composer = file_get_contents(__dir__ . '/composer.json');
            $composer = json_decode($composer);
            self::$variables[self::VERSION] = $composer->version;
            self::$variables[self::DESCRIPTION] = $composer->description;
            self::$variables[self::HOMEPAGE] = $composer->homepage;
            self::$variables[self::DEBUG] = DEBUGGING;
        }
    }
}
