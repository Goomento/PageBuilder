<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\ViewModel;

use Goomento\Core\Model\Registry;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class PageContent
 * @package Goomento\PageBuilder\ViewModel
 */
class PageContent implements ArgumentInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * PageContent constructor.
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    )
    {
        $this->registry = $registry;
    }

    /**
     * @return ContentInterface|null
     */
    public function getContent()
    {
        return $this->registry->registry('pagebuilder_content');
    }
}
