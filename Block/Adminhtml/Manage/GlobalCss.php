<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Manage;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Backend\Block\Template;
use Goomento\PageBuilder\Model\Config;
use Goomento\PageBuilder\Api\ConfigInterface;

class GlobalCss extends Tab
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param Template\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        DataPersistorInterface $dataPersistor,
        ConfigInterface $config,
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCustomCss()
    {
        if ($css = $this->dataPersistor->get(Config::CUSTOM_CSS)) {
            $this->dataPersistor->clear(Config::CUSTOM_CSS);
            return $css;
        }

        return (string) $this->config->getValue(Config::CUSTOM_CSS);
    }
}
