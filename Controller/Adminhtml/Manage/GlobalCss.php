<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Manage;

use Exception;
use Goomento\PageBuilder\Helper\EscaperHelper;
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
            $data = EscaperHelper::filter($data);
            if (isset($data[Config::CUSTOM_CSS]) && $data[Config::CUSTOM_CSS] !== $this->config->getValue(Config::CUSTOM_CSS)) {
                $customCss = trim($data['custom_css']);
                $this->dataPersistor->set(Config::CUSTOM_CSS, $customCss);
                $this->config->setValue(Config::CUSTOM_CSS, $customCss);
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
}
