<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\SampleImport\Samples;

use Goomento\PageBuilder\Api\Data\SampleImportInterface;
use Goomento\PageBuilder\Helper\AssetsHelper;

class Luma implements SampleImportInterface
{
    /**
     * @return string
     */
    protected static function getFixturePath()
    {
        return AssetsHelper::getModulePath('Goomento_PageBuilder') . '/SampleImport/fixture/luma';
    }

    /**
     * @inheritDoc
     */
    public function getMediaDir()
    {
        return self::getFixturePath() . '/media';
    }

    /**
     * @inheritDoc
     */
    public function getSourceFiles()
    {
        return self::getFixturePath();
    }

    /**
     * @inheritDoc
     */
    public function getReplacement()
    {
        return [
            "http:\\/\\/goomento-pagebuilder.site\\/media\\/luma\\/" => AssetsHelper::pathToUrl('media/luma/')
        ];
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return __('Luma');
    }
}
