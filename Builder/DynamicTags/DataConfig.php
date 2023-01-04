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
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;

class DataConfig extends AbstractDataTag
{
    const NAME = 'data_config';

    const CONFIG_PATH = 'config_path';

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [Tags::TEXT_CATEGORY];
    }

    /**
     * @inheritDoc
     */
    public function getGroup()
    {
        return [Tags::TEXT_CATEGORY];
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return (string) __('Config Path');
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->addControl(
            self::CONFIG_PATH,
            [
                'label' => __('Config Path'),
                'type' => Controls::TEXT,
                'default' => '',
                'dynamic' => [
                    'active' => false
                ]
            ]
        );
    }


    /**
     * @inheritDoc
     */
    public function getPanelTemplateSettingKey()
    {
        return self::CONFIG_PATH;
    }

    /**
     * @inheritDoc
     */
    protected function getValue(array $options = [])
    {
        $path = $this->getSettings(self::CONFIG_PATH);
        if (trim($path) && count(explode('/', $path)) >= 3) {

            $data = DataHelper::getConfig($path);

            if ($data && !is_scalar($data)) {
                throw new BuilderException(
                    'Invalid config path'
                );
            }

            return (string) $data;
        }
        return '';
    }
}
