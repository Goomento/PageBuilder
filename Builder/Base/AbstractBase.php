<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;

abstract class AbstractBase extends AbstractEntity
{
    /**
     * Shared type
     */
    const TYPE = 'base';

    /**
     * Shared name
     */
    const NAME = 'base';

    /**
     * @var
     */
    private $settings;

    /**
     * @param null $setting
     * @return array|mixed|null
     */
    public function getSettings($setting = null)
    {
        $this->ensureSettings();

        return self::getItems($this->settings, $setting);
    }

    /**
     * @param $key
     * @param null $value
     */
    public function setSettings($key, $value = null)
    {
        $this->ensureSettings();

        if (is_array($key)) {
            $this->settings = $key;
        } else {
            $this->settings[ $key ] = $value;
        }
    }

    /**
     * @param null $key
     */
    public function deleteSetting($key = null)
    {
        if ($key) {
            unset($this->settings[ $key ]);
        } else {
            $this->settings = [];
        }
    }

    /**
     * @param array $haystack
     * @param null $needle
     * @return array|mixed|null
     */
    protected static function getItems(array $haystack, $needle = null)
    {
        if ($needle) {
            return DataHelper::arrayGetValue($haystack, $needle, '.');
        }

        return $haystack;
    }

    /**
     * Init settings
     *
     * @return array
     */
    protected function getInitSettings()
    {
        return [];
    }

    /**
     * Ensure settings
     */
    private function ensureSettings()
    {
        if (null === $this->settings) {
            $this->settings = $this->getInitSettings();

            $this->settings = HooksHelper::applyFilters(
                'pagebuilder/settings/' . static::TYPE . '/' . static::NAME,
                $this->settings
            )->getResult();
        }
    }
}
