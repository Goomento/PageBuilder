<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api;

use Magento\Framework\Exception\LocalizedException;

/**
 * Interface ContentManagementInterface
 * @package Goomento\PageBuilder\Api
 */
interface ContentManagementInterface
{
    /**
     * @return Data\RevisionInterface
     * @throws LocalizedException
     */
    public function createRevision(Data\ContentInterface $content, $status = Data\RevisionInterface::STATUS_REVISION);

    /**
     * Export content via http download
     *
     * @param Data\ContentInterface $content
     */
    public function exportContent(Data\ContentInterface $content) : void;

    /**
     * @param array $data
     * @return Data\ContentInterface
     * @throws LocalizedException
     */
    public function createContent(array $data) : Data\ContentInterface;

    /**
     * @param Data\ContentInterface|int $content
     * @return mixed
     */
    public function refreshContentCache($content);

    /**
     * @return mixed
     */
    public function refreshAllContentCache();

    /**
     * @return mixed
     */
    public function refreshGlobalCache();
}
