<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\PageBuilder\Model\ResourceModel;

use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Model\ResourceModel\Content as ContentResourceModel;
use Magento\Framework\Model\AbstractModel;

class Content
{
    /**
     * @var ContentRegistryInterface
     */
    private $contentRegistry;

    /**
     * @param ContentRegistryInterface $contentRegistry
     */
    public function __construct(
        ContentRegistryInterface $contentRegistry
    )
    {
        $this->contentRegistry = $contentRegistry;
    }

    /**
     * @param ContentResourceModel $subject
     * @param $result
     * @param AbstractModel $object
     * @return void
     */
    public function afterSave(
        ContentResourceModel $subject,
        $result,
        AbstractModel $object
    )
    {
        if ($object instanceof ContentInterface) {
            $this->contentRegistry->invalidateContent($object);
        }

        return $result;
    }

    /**
     * @param ContentResourceModel $subject
     * @param $result
     * @param AbstractModel $object
     * @return void
     */
    public function afterDelete(
        ContentResourceModel $subject,
        $result,
        AbstractModel $object
    )
    {
        if ($object instanceof ContentInterface) {
            $this->contentRegistry->invalidateContent($object);
        }

        return $result;
    }
}
