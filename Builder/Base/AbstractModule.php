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

    const TYPE = 'module';

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
        throw new \Goomento\PageBuilder\Exception\BuilderException(
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
     * @param string $fileName
     * @param string $fileExtension
     * @param string $relativeUrl Optional. Default is null.
     * @param string $addMinSuffix Optional. Default is 'default'.
     *
     * @return string
     */
    protected function getAssetsUrl($fileName, $fileExtension, $relativeUrl = null, $addMinSuffix = 'default')
    {
        if (!$relativeUrl) {
            $relativeUrl = $this->getAssetsRelativeUrl() . $fileExtension . '/';
        }

        $url = $this->getAssetsBaseUrl() . $relativeUrl . $fileName;

        if ('default' === $addMinSuffix) {
            $addMinSuffix = false;
        }

        if ($addMinSuffix) {
            $url .= '.min';
        }

        $url .= '.' . $fileExtension;

        return $url;
    }

    /**
     * Get js assets url
     *
     *
     * @param string $fileName
     * @param string $relativeUrl Optional. Default is null.
     * @param string $addMinSuffix Optional. Default is 'default'.
     *
     * @return string
     * @deprecated
     */
    protected function getJsAssetsUrl($fileName, $relativeUrl = null, $addMinSuffix = 'default'): string
    {
        return $this->getAssetsUrl($fileName, 'js', $relativeUrl, $addMinSuffix);
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
