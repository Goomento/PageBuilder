<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Config\Frontend;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Module\ResourceInterface;

class Version extends Template implements RendererInterface
{
    /**
     * @inheirtDoc
     */
    protected $_template = 'Goomento_PageBuilder::system/version.phtml';
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @param Template\Context $context
     * @param ResourceInterface $moduleResource
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ResourceInterface $moduleResource,
        array $data = []
    )
    {
        $this->moduleResource = $moduleResource;
        parent::__construct($context, $data);
    }

    /**
     * @return false|string
     */
    public function getPageBuilderVersion()
    {
        return $this->moduleResource->getDbVersion('Goomento_PageBuilder');
    }

    /**
     * @inheritDoc
     */
    public function render(AbstractElement $element)
    {
        return $this->toHtml();
    }
}
