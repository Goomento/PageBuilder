<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets\Magento;

use Goomento\PageBuilder\Block\Cms\Page;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Magento\Cms\Model\Config\Source\Block;

class CmsPage extends AbstractMagentoWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'magento-cms-page';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('CMS Page');
    }

    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerCmsPageWidgetInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        /** @var Block $source */
        $source = ObjectManagerHelper::get(\Magento\Cms\Model\Config\Source\Page::class);
        $options = [];
        foreach ($source->toOptionArray() as $item) {
            $options[$item['value']] = $item['label'];
        }
        $widget->addControl(
            $prefix . 'id',
            [
                'label' => __('Page'),
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
            'cms_page_section',
            [
                'label' => __('Cms Page'),
            ]
        );

        self::registerCmsPageWidgetInterface($this);

        $this->endControlsSection();
    }

    /**
     * @inheritDoc
     */
    protected function render()
    {
        $value = $this->getSettingsForDisplay(self::NAME . '_id');
        if ($value) {
            return ObjectManagerHelper::create(Page::class, ['data' => [
                'page_id' => $value
            ]])->toHtml();
        } else {
            return '';
        }
    }
}
