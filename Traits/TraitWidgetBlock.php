<?php
/**
 * @package Goomento_Core
 * @link https://github.com/Goomento/Core
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Traits;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Model\ContentDataProcessor;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
trait TraitWidgetBlock
{
    /**
     * @return AbstractWidget|null
     */
    public function getWidget() : ?AbstractWidget
    {
        return $this->getData('builder_widget');
    }

    /**
     * @return Escaper
     */
    public function getEscaper() : Escaper
    {
        if (isset($this->_escaper) && $this->_escaper instanceof Escaper) {
            return $this->_escaper;
        }

        return ObjectManagerHelper::get(Escaper::class);
    }

    /**
     * @return mixed
     */
    public function setWidget(AbstractWidget $widget) : Template
    {
        return $this->setData('builder_widget', $widget);
    }

    /**
     * @return array|string
     */
    public function getSettingsForDisplay(?string $key = null)
    {
        if (empty($settings = $this->getData('settings')) && $this->getWidget()) {
            $settings = $this->getWidget()->getSettingsForDisplay();
            $this->setData('settings', $settings);
        }

        return $key ? ($settings[$key] ?? []) : $settings;
    }

    /**
     * @param string $prefix
     * @param array $settings
     * @return array
     */
    public static function parseSettingsByPrefix(string $prefix, array $settings) : array
    {
        $result = [];
        foreach ($settings as $key => $value) {
            $parsedKey = $key;

            if ($value instanceof Phrase) {
                $value = $value->__toString();
            }

            if ($value instanceof DataObject) {
                $value = $value->toArray();
            }

            if (is_string($parsedKey) && substr($key, 0, strlen($prefix)) === $prefix) {
                $parsedKey = substr($key, strlen($prefix));
            }

            if (!is_scalar($value)) {
                $parsedValue = self::parseSettingsByPrefix($prefix, (array) $value);
            } else {
                $testedValue = substr((string) $value, 0, strlen($prefix)) === $prefix;
                $parsedValue = $testedValue ? substr($value, strlen($prefix)) : $value;
            }

            $result[$parsedKey] = $parsedValue;
        }

        return $result;
    }

    /**
     * @param string $widgetName
     * @param string|null $configKey For retrieve data to child from parent widget
     * @param string|null $linkSettingKey For connect parent-child widget in editor, such as inline editor setting
     * @param array|null $settings
     * @return string
     */
    public function getWidgetHtml(
        string $widgetName,
        string $configKey = null,
        string $linkSettingKey = null,
        array $settings = null
    ) : string {
        /** @var null|ContentDataProcessor $contentDataProcessor */
        $contentDataProcessor = $this->getData('content_data_processor');
        if (null === $contentDataProcessor) {
            $contentDataProcessor = ObjectManagerHelper::get(ContentDataProcessor::class);
            $this->setData('content_data_processor', $contentDataProcessor);
        }

        if (null === $settings) {
            $settings = $this->getSettingsForDisplay();
        }

        $configKey = $this->getWidget()->buildPrefixKey($configKey);
        if (null === $linkSettingKey) {
            $linkSettingKey = $configKey;
        }
        $data = [];
        $data['elType'] = 'widget';
        $data['widgetType'] = $widgetName;
        $data['id'] = $this->getWidget()->getId() . '-' . $widgetName;
        $data['elements'] = [];
        $data['prefix_setting_key'] = $linkSettingKey;
        $data['settings'] = $this->parseSettingsByPrefix($configKey, $settings);

        return $contentDataProcessor->getElementHtml($this->getContent(), $data);
    }
}
