<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api;

use Magento\Framework\Exception\LocalizedException;

interface ContentManagementInterface
{
    /**
     * @return Data\RevisionInterface|null
     * @throws LocalizedException
     */
    public function createRevision(Data\ContentInterface $content, $status = Data\RevisionInterface::STATUS_REVISION);

    /**
     * Export content via http download
     *
     * @param Data\ContentInterface $content
     */
    public function httpContentExport(Data\ContentInterface $content) : void;

    /**
     * @param array $data
     * @return Data\ContentInterface
     * @throws LocalizedException
     */
    public function createContent(array $data) : Data\ContentInterface;

    /**
     * @param Data\ContentInterface $content
     * @return mixed
     */
    public function refreshContentAssets(Data\ContentInterface $content);

    /**
     * @return mixed
     */
    public function refreshGlobalAssets();
}
