<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;


use Goomento\Core\Traits\TraitStaticInstances;

/**
 * Class StaticTemplate
 * @package Goomento\PageBuilder\Helper
 */
class StaticTemplate
{
    use TraitStaticInstances;

    /**
     * @param $path
     * @param array $params
     * @return string
     */
    public static function getHtml($path, $params = [])
    {
        $data = [];
        $data['data'] = $params;
        if (!class_exists($path)) {
            $block = \Magento\Framework\View\Element\Template::class;
            $data = [
                'template' => $path,
            ];
        } else {
            $block = $path;
        }
        /** @var \Magento\Framework\View\LayoutInterface $layout */
        $layout = self::getInstance(\Magento\Framework\View\LayoutInterface::class);
        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $layout->createBlock($block, '', $data);
        if (isset($data['template'])) {
            $block->setTemplate($data['template']);
        }

        return $block->toHtml();
    }
}
