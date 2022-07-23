<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Content;

use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Controller\AbstractAction;
use Goomento\PageBuilder\Traits\TraitHttpPage;
use Magento\Framework\Exception\LocalizedException;

/**
 * For Admin Preview purpose, therefore, there is all content will be rendered
 * This page will not be cached
 */
class View extends AbstractAction
{
    use TraitHttpPage;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            HooksHelper::doAction('pagebuilder/content/view', $this->getContent(true));
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
        return [
            'editable_title' => '%1',
            'handler' => $this->getContentLayout(true) ?: 'pagebuilder_content_1column',
        ];
    }
}
