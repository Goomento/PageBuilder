<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Manage;

use Goomento\PageBuilder\Controller\Adminhtml\AbstractAction;
use Goomento\PageBuilder\Traits\TraitHttpGet;
use Goomento\PageBuilder\Traits\TraitHttpPage;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Tabs extends AbstractAction implements HttpGetActionInterface
{
    use TraitHttpGet;
    use TraitHttpPage;

    /**
     * @inheritdoc
     */
    const ADMIN_RESOURCE = 'Goomento_PageBuilder::manage';

    /**
     * @inheritDoc
     */
    protected function executeGet()
    {
        return $this->renderPage();
    }

    /**
     * @inheritDoc
     */
    protected function getPageConfig()
    {
        return [
            'title' => __('Management'),
            'active_menu' => 'Goomento_PageBuilder::manage',
        ];
    }
}
