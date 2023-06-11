<?php
/**
 * @package Goomento_Core
 * @link https://github.com/Goomento/Core
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Traits;

use Goomento\PageBuilder\Helper\ObjectManagerHelper;

trait TraitComponentsLoader
{
    protected $components;

    /**
     * @return array
     */
    protected function getComponents(): array
    {
        $components = (array) $this->components;
        foreach ($components as $name => $model) {
            if (is_string($model) && class_exists($model)) {
                $this->getComponent($name);
            }
        }

        return $this->components ?? [];
    }

    /**
     * @param string|null $name
     * @return mixed|null
     */
    protected function getComponent(?string $name)
    {
        if ($name !== null) {
            $model = $this->components[$name] ?? null;
            if (is_string($model) && class_exists($model)) {
                $this->components[$name] = ObjectManagerHelper::get(
                    $model
                );
            }
        } else {
            return $this->getComponents();
        }

        return $this->components[$name] ?? null;
    }

    /**
     * @param $name
     * @param $model
     * @return $this
     */
    protected function setComponent($name, $model = null)
    {
        if (is_array($name)) {
            foreach ($name as $item => $value) {
                $this->setComponent($item, $value);
            }
        } else {
            $this->components[$name] = $model;
        }

        return $this;
    }

    /**
     * @param string|null $name
     * @return bool
     */
    protected function removeComponent(?string $name)
    {
        if (null === $name) {
            $this->components = [];
            return true;
        } elseif (isset($this->components[$name])) {
            unset($this->components[$name]);
            return true;
        }

        return false;
    }
}
