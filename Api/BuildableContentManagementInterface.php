<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;

interface BuildableContentManagementInterface
{
    /**
     * Export content via http download
     *
     * @param Data\ContentInterface $content
     */
    public function httpContentExport(Data\ContentInterface $content) : void;

    /**
     * Update the Content CSS to the newest state
     *
     * @param BuildableContentInterface $buildableContent
     * @return void
     */
    public function refreshBuildableContentAssets(BuildableContentInterface $buildableContent);

    /**
     * Update the Global CSS to the newest state
     *
     * @return void
     */
    public function refreshGlobalAssets();

    /**
     * Replace Urls of contents
     *
     * @param string $find
     * @param string $replace
     * @param ContentInterface|int|null $content
     * @return void
     */
    public function replaceUrls(string $find, string $replace, $content = null);

    /**
     * Save content also make a revision if capable
     *
     * @param BuildableContentInterface $buildableContent
     * @param string $saveMassage
     * @return BuildableContentInterface
     */
    public function saveBuildableContent(BuildableContentInterface $buildableContent, string $saveMassage = '') : BuildableContentInterface;

    /**
     * @param BuildableContentInterface $buildableContent
     * @return void
     */
    public function deleteBuildableContent(BuildableContentInterface $buildableContent);

    /**
     * @param string $buildableType
     * @param array|BuildableContentInterface $params
     * @return ContentInterface|RevisionInterface|BuildableContentInterface|null
     */
    public function buildBuildableContent(string $buildableType = ContentInterface::CONTENT, $params = []) : ?BuildableContentInterface;
}
