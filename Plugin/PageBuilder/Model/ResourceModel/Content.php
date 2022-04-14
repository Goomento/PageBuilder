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
use Goomento\PageBuilder\Model\PageBuilderUrlRewriteGenerator;
use Magento\Framework\Model\AbstractModel;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Content
{
    /**
     * @var ContentRegistryInterface
     */
    private $contentRegistry;
    /**
     * @var UrlPersistInterface
     */
    private $urlPersist;

    /**
     * @param ContentRegistryInterface $contentRegistry
     * @param UrlPersistInterface $urlPersist
     */
    public function __construct(
        ContentRegistryInterface $contentRegistry,
        UrlPersistInterface $urlPersist
    )
    {
        $this->contentRegistry = $contentRegistry;
        $this->urlPersist = $urlPersist;
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
            if ($object->getType() === ContentInterface::TYPE_PAGE) {
                $this->urlPersist->deleteByData([
                    UrlRewrite::ENTITY_ID => $object->getId(),
                    UrlRewrite::ENTITY_TYPE => PageBuilderUrlRewriteGenerator::ENTITY_TYPE,
                ]);
            }
        }

        return $result;
    }
}
