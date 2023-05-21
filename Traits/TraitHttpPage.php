<?php
/**
 * @package Goomento_Core
 * @link https://github.com/Goomento/Core
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Traits;

use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

trait TraitHttpPage
{
    /**
     * @var array
     */
    private $defaultParams = [
        'active_menu' => '',
        'title' => '',
        'editable_title' => '',
        'handler' => '',
        'breadcrumb' => [
        ],
    ];

    /**
     * @var PageFactory|null
     */
    protected $pageFactory;

    use TraitHttpContentAction;

    /**
     * @return Page
     * @throws LocalizedException
     */
    protected function renderPage(): Page
    {
        $pageConfig = array_merge($this->defaultParams, (array) $this->getPageConfig());

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

            $text = $content && $content->getId() ?
                __($pageConfig['editable_title'], $content->getTitle()) : $pageConfig['title'];

            if (!$title->get()) {
                $title->set(
                    $text
                );
            } else {
                $title->prepend(
                    $text
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
        if (null === $this->pageFactory) {
            $this->pageFactory = ObjectManagerHelper::get(PageFactory::class);
        }

        return $this->pageFactory->create();
    }

    /**
     * Get page configuration for display
     * @return array
     */
    abstract protected function getPageConfig();
}
