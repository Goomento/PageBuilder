<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Manage;

use Exception;
use Goomento\PageBuilder\Model\Config;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;

class GlobalCss extends AbstractManage
{
    /**
     * @inheritdoc
     */
    const ADMIN_RESOURCE = 'Goomento_PageBuilder::manage_global_css';

    /**
     * @inheritdoc
     */
    protected function executePost()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $data = (new \Zend_Filter_Input([], [], $data))->getUnescaped();
            if (isset($data[Config::CUSTOM_CSS]) && $data[Config::CUSTOM_CSS] !== $this->config->getOption(Config::CUSTOM_CSS)) {
                $this->dataPersistor->set(Config::CUSTOM_CSS, $data['custom_css']);
                $this->config->setOption(Config::CUSTOM_CSS, $data['custom_css']);
            }

            $this->contentManagement->refreshGlobalAssets();

            $this->messageManager->addSuccessMessage(__('Global.css saved.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addErrorMessage(__('Something went wrong when saving Global.css'));
        }

        /** @var Redirect $resultRedirect */
        $result = $this->resultRedirectFactory->create();
        return $result->setRefererUrl();
    }

    /**
     * @return array
     */
    protected static function getPageConfig()
    {
        return [
            'title' => __('Global.css'),
        ];
    }
}
