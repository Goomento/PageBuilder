<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticInstances;
use Goomento\PageBuilder\Block\View\Element\Widget;
use Goomento\PageBuilder\Builder\Base\AbstractWidget as WidgetBase;

class TemplateHelper
{
    use TraitStaticInstances;

    /**
     * @param $template
     * @param array $params
     * @return string
     */
    public static function getHtml($template, array $params = []): string
    {
        $arguments = [];
        $arguments['data'] = $params;
        if (!class_exists($template)) {
            $block = \Magento\Framework\View\Element\Template::class;
            $arguments['data']['template'] = $template;
        } else {
            $block = $template;
        }
        $block = LayoutHelper::createBlock($block, '', $arguments);

        return $block->toHtml();
    }

    /**
     * @param WidgetBase $widget
     * @param $templatePath
     * @param array $params
     * @return string
     */
    public static function getWidgetHtml(WidgetBase $widget, $templatePath, array $params = []): string
    {
        $params = array_merge($params, [
            'template' => $templatePath,
            'widget' => $widget
        ]);
        return self::getHtml(Widget::class, $params);
    }
}
