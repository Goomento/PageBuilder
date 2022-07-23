<?php

namespace Goomento\PageBuilder\Builder\DynamicTags;

use Goomento\PageBuilder\Builder\Base\AbstractDataTag;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Magento\Theme\Block\Html\Header\Logo;

class Images extends AbstractDataTag
{
    const NAME = 'image';

    const IMAGE = self::NAME;

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [Tags::IMAGE_CATEGORY];
    }

    /**
     * @inheritDoc
     */
    public function getGroup()
    {
        return [Tags::IMAGE_CATEGORY];
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return (string)__('Images');
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->addControl(
            self::IMAGE,
            [
                'label' => __('Images'),
                'type' => Controls::SELECT,
                'default' => 'logo',
                'options' => [
                    'logo' => __('Logo')
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function getValue(array $options = [])
    {
        $path = $this->getSettings(self::IMAGE);

        switch ($path) {
            case 'logo':
                /** @var Logo $storeManager */
                $logo = ObjectManagerHelper::get(Logo::class);
                return [
                    'url' => $logo->getLogoSrc()
                ];
            default:
                return '';
        }
    }
}
