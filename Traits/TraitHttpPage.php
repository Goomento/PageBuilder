<?php
/**
 * @package Goomento_Core
 * @link https://github.com/Goomento/Core
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Traits;

use Goomento\Core\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Trait TraitHttpPage
 * @package Goomento\PageBuilder\Traits
 */
trait TraitHttpPage
{
    use TraitHttpContentAction;

    /**
     * @return Page
     * @throws LocalizedException
     */
    protected function renderPage()
    {
        $pageConfig = array_merge([
            'active_menu' => '',
            'title' => '',
            'editable_title' => '',
            'handler' => '',
            'breadcrumb' => [
            ],
        ], (array) $this->getPageConfig());

        $resultPage = $this->createPageResult();

        if (!empty($pageConfig['handler'])) {
            $resultPage->addHandle($pageConfig['handler']);
        }

        if (!empty($pageConfig['active_menu'])) {
            $resultPage->setActiveMenu($pageConfig['active_menu']);
        }

        if (!empty($pageConfig['breadcrumb'])) {
            foreach ($pageConfig['breadcrumb'] as $args) {
                $resultPage->addBreadcrumb(...$args);
            }
        }

        if (!empty($pageConfig['title']) || !empty($pageConfig['editable_title'])) {
            $content = $this->getContent();
            $title = $resultPage->getConfig()->getTitle();
            if (!empty($pageConfig['breadcrumb'])) {
                $title->prepend(
                    $content && $content->getId() ?
                        __($pageConfig['editable_title'], $content->getTitle()) :
                        $pageConfig['title']
                );
            } else {
                $title->set(
                    $content && $content->getId() ?
                        __($pageConfig['editable_title'], $content->getTitle()) :
                        $pageConfig['title']
                );
            }
        }

        return $resultPage;
    }

    /**
     * @return Page
     */
    private function createPageResult()
    {
        if (!isset($this->pageFactory)) {
            $this->pageFactory = ObjectManager::get(PageFactory::class);
        }

        return $this->pageFactory->create();
    }

    /**
     * Get page configuration for display
     * @return array
     */
    abstract protected function getPageConfig();
}
