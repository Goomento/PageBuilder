<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Manage;


use Goomento\PageBuilder\Helper\StaticConfig;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class GlobalCss
 * @package Goomento\PageBuilder\Controller\Adminhtml\Manage
 */
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
            if (isset($data['custom_css']) && $data['custom_css'] !== StaticConfig::getThemeOption('custom_css')) {
                StaticConfig::updateThemeOption('custom_css', $data['custom_css']);
            }

            /** Update global.css file */
            /** @var \Goomento\PageBuilder\PageBuilder $pagebuilder */
            $pagebuilder = StaticObjectManager::get(\Goomento\PageBuilder\PageBuilder::class);
            $pagebuilder->init();

            /** @var \Goomento\PageBuilder\Core\Files\Css\GlobalCss $globalCss */
            $globalCss = StaticObjectManager::create(\Goomento\PageBuilder\Core\Files\Css\GlobalCss::class);
            $globalCss->updateFile();

            $this->messageManager->addSuccessMessage(__('Global.css saved.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong when saving Global.css'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
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
