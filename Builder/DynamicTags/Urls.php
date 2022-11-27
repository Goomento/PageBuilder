<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DynamicTags;

use Goomento\PageBuilder\Builder\Base\AbstractDataTag;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Magento\Store\Model\StoreManagerInterface;

class Urls extends AbstractDataTag
{
    const NAME = 'url';

    const URL = self::NAME;

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [Tags::URL_CATEGORY];
    }

    /**
     * @inheritDoc
     */
    public function getGroup()
    {
        return [Tags::URL_CATEGORY];
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return (string)__('URLs');
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->addControl(
            self::URL,
            [
                'label' => __('URLs'),
                'type' => Controls::SELECT,
                'default' => 'home',
                'options' => [
                    'home' => __('Home Page')
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function getValue(array $options = [])
    {
        $path = $this->getSettings(self::URL);

        switch ($path) {
            case 'home':
                /** @var StoreManagerInterface $storeManager */
                $storeManager = ObjectManagerHelper::get(StoreManagerInterface::class);
                $baseUrl = $storeManager->getStore()->getBaseUrl();
                return [
                'url' => $baseUrl
                ];
            default:
                return '';
        }
    }
}
