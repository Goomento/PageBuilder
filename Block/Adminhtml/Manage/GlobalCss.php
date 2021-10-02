<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

namespace Goomento\PageBuilder\Block\Adminhtml\Manage;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Backend\Block\Template;
use Goomento\PageBuilder\Model\Config;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class GlobalCss
 * @package Goomento\PageBuilder\Block\Adminhtml\Manage
 */
class GlobalCss extends Template
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Template\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        DataPersistorInterface $dataPersistor,
        Config $config,
        array $data = []
    )
    {
        $this->dataPersistor = $dataPersistor;
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getCustomCss()
    {
        if ($css = $this->dataPersistor->get(Config::CUSTOM_CSS)) {
            $this->dataPersistor->clear(Config::CUSTOM_CSS);
            return $css;
        }

        return (string) $this->config->getOption(Config::CUSTOM_CSS);
    }
}
