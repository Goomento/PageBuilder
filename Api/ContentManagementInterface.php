<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\Exception\LocalizedException;

interface ContentManagementInterface
{
    /**
     * Quick create Page Builder Revision
     *
     * @return Data\RevisionInterface|null
     * @throws LocalizedException
     */
    public function createRevision(Data\ContentInterface $content, $status = Data\BuildableContentInterface::STATUS_REVISION);

    /**
     * Export content via http download
     *
     * @param Data\ContentInterface $content
     */
    public function httpContentExport(Data\ContentInterface $content) : void;

    /**
     * Quick create Page Builder Content
     *
     * @param array $data
     * @return Data\ContentInterface
     * @throws LocalizedException
     */
    public function createContent(array $data) : Data\ContentInterface;

    /**
     * Update the Content CSS to the newest state
     *
     * @param Data\ContentInterface $content
     * @return void
     */
    public function refreshContentAssets(Data\ContentInterface $content);

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
}
