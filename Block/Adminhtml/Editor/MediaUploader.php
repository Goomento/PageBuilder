<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Editor;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Gallery\DefaultConfigProvider;

class MediaUploader extends Template
{
    /**
     * @var DefaultConfigProvider
     */
    private $defaultConfigProvider;

    /**
     * @param Context $context
     * @param DefaultConfigProvider $defaultConfigProvider
     * @param array $data
     */
    public function __construct(
        Context          $context,
        DefaultConfigProvider $defaultConfigProvider,
        array            $data = []
    ) {
        $this->defaultConfigProvider = $defaultConfigProvider;
        parent::__construct($context, $data);
    }

    /**
     * @param string $htmlId
     * @param int|null $storeId
     * @return string
     */
    public function getMediaUploadUrl(string $htmlId, ?int $storeId = null)
    {
        $config = new \Magento\Framework\DataObject();
        $this->defaultConfigProvider->getConfig($config);
        return $config->getData('files_browser_window_url')
            . 'target_element_id/'
            . $htmlId
            . '/'
            . (null !== $storeId
                ? 'store/' . $storeId . '/"'
                : '');
    }
}
