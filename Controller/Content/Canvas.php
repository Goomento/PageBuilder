<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Content;

use Goomento\PageBuilder\Helper\HooksHelper;
use Magento\Framework\Exception\LocalizedException;

/**
 * For Editor Preview purpose, therefore, there is no content should be rendered
 * This page will not be cached
 */
class Canvas extends View
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            HooksHelper::doAction('pagebuilder/content/canvas', $this->getContent(true));
            return $this->renderPage();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong when render content view.')
            );
            $this->logger->error($e);
        } finally {
            if (!empty($e)) {
                if ($this->dataHelper->isDebugMode()) {
                    throw $e;
                }
            }
        }

        return $this->redirect404Page();
    }

    /**
     * @inheritdoc
     */
    protected function getPageConfig()
    {
        $layout = $this->getRequest()->getParam('layout');
        if (!$layout) {
            $layout = $this->getContentLayout() ?: 'pagebuilder_content_1column';
        }

        return [
            'editable_title' => 'Preview: %1',
            'handler' =>$layout
        ];
    }
}
