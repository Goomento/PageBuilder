<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Block\Content;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Goomento\PageBuilder\Model\Config\Source\PageList;

class PageBuilder extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'gmt_content';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Page Builder');
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return ['goomento', 'page builder', 'page', 'builder', 'content'];
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return ['general'];
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'goomento-logo-grey';
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerPageBuilderContentInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        /** @var PageList $source */
        $source = ObjectManagerHelper::get(PageList::class);
        $options = [];
        foreach ($source->toOptionArray() as $item) {
            $options[$item['value']] = $item['label'];
        }
        $widget->addControl(
            $prefix . 'id',
            [
                'label' => __('Content'),
                'type' => Controls::SELECT2,
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
            'pagebuilder_content_section',
            [
                'label' => __('Content'),
            ]
        );

        self::registerPageBuilderContentInterface($this);

        $this->endControlsSection();
    }

    /**
     * @inheritDoc
     */
    protected function render()
    {
        $identifier = $this->getSettingsForDisplay(static::NAME . '_id');
        if ($identifier) {
            return StateHelper::emulateFrontend(function () use($identifier) {
                /** @var Content $builder */
                $builder = ObjectManagerHelper::get(Content::class);
                $builder->setIdentifier($identifier);
                return $builder->toHtml();
            });
        } else {
            return '';
        }
    }
}
