<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

abstract class AbstractModule extends AbstractBase
{
    /**
     * Module components.
     *
     * Holds the module components.
     *
     *
     * @var array
     */
    private $components = [];

    /**
     * Module instance.
     *
     * Holds the module instance.
     *
     *
     * @var AbstractModule
     */
    protected static $_instances = [];


    public static function isActive()
    {
        return true;
    }

    /**
     * Clone.
     *
     * Disable class cloning and throw an error on object clone.
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object. Therefore, we don't want the object to be cloned.
     *
     */
    public function __clone()
    {
        throw new \Exception('Something went wrong');
    }

    /**
     * Wakeup.
     *
     * Disable unserializing of the class.
     *
     */
    public function __wakeup()
    {
        throw new \Exception(
            'Something went wrong'
        );
    }

    /**
     * Add module component.
     *
     * Add new component to the current module.
     *
     *
     * @param string $id Component ID.
     * @param mixed $instance An instance of the component.
     */
    public function addComponent(string $id, $instance)
    {
        $this->components[ $id ] = $instance;
    }

    /**
     * @return AbstractModule[]
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * Get module component.
     *
     * Retrieve the module component.
     *
     *
     * @param string $id Component ID.
     *
     * @return mixed An instance of the component, or `false` if the component
     *               doesn't exist.
     */
    public function getComponent($id)
    {
        if (isset($this->components[ $id ])) {
            return $this->components[ $id ];
        }

        return false;
    }

    /**
     * Get assets url.
     *
     *
     * @param string $file_name
     * @param string $file_extension
     * @param string $relative_url Optional. Default is null.
     * @param string $add_min_suffix Optional. Default is 'default'.
     *
     * @return string
     */
    final protected function getAssetsUrl($file_name, $file_extension, $relative_url = null, $add_min_suffix = 'default')
    {
        if (!$relative_url) {
            $relative_url = $this->getAssetsRelativeUrl() . $file_extension . '/';
        }

        $url = $this->getAssetsBaseUrl() . $relative_url . $file_name;

        if ('default' === $add_min_suffix) {
            $add_min_suffix = false;
        }

        if ($add_min_suffix) {
            $url .= '.min';
        }

        $url .= '.' . $file_extension;

        return $url;
    }

    /**
     * Get js assets url
     *
     *
     * @param string $file_name
     * @param string $relative_url Optional. Default is null.
     * @param string $add_min_suffix Optional. Default is 'default'.
     *
     * @return string
     * @deprecated
     */
    final protected function getJsAssetsUrl($file_name, $relative_url = null, $add_min_suffix = 'default'): string
    {
        return $this->getAssetsUrl($file_name, 'js', $relative_url, $add_min_suffix);
    }

    /**
     * Get assets base url
     *
     *
     * @return string
     */
    protected function getAssetsBaseUrl()
    {
        return 'Goomento_PageBuilder';
    }

    /**
     * Get assets relative url
     *
     *
     * @return string
     */
    protected function getAssetsRelativeUrl()
    {
        return '/';
    }
}
