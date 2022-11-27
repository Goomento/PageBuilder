<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets\Magento;

use Goomento\PageBuilder\Block\Cms\Block;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class CmsBlock extends AbstractMagentoWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'magento-cms-block';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('CMS Block');
    }

    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param array $args
     * @return void
     */
    public static function registerCmsBlockWidgetInterface(AbstractWidget $widget, string $prefix = self::NAME . '_', array $args = [])
    {
        /** @var \Magento\Cms\Model\Config\Source\Block $source */
        $source = ObjectManagerHelper::get(\Magento\Cms\Model\Config\Source\Block::class);
        $options = [];
        foreach ($source->toOptionArray() as $item) {
            $options[$item['value']] = $item['label'];
        }
        $widget->addControl(
            $prefix . 'id',
            $args + [
                'label' => __('Block'),
                'type' => Controls::SELECT,
                'default' => '',
                'options' => $options
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'cms_block_section',
            [
                'label' => __('Cms Block'),
            ]
        );

        self::registerCmsBlockWidgetInterface($this);

        $this->endControlsSection();
    }

    /**
     * @inheritDoc
     */
    protected function render()
    {
        $value = $this->getSettingsForDisplay(self::NAME . '_id');
        if ($value) {
            return ObjectManagerHelper::create(Block::class, ['data' => [
                'block_id' => $value
            ]])->toHtml();
        } else {
            return '';
        }
    }
}
