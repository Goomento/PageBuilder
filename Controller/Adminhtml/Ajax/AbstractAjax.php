<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Ajax;

use Goomento\PageBuilder\Controller\Adminhtml\AbstractAction;
use Goomento\Core\Traits\TraitHttpAction;
use Goomento\PageBuilder\Helper\HooksHelper;
use Magento\Framework\App\RequestInterface;

abstract class AbstractAjax extends AbstractAction
{
    use TraitHttpAction;

    /**
     * @inheritdoc
     */
    public function dispatch(RequestInterface $request)
    {
        if ($request->isAjax() === true) {
            HooksHelper::doAction('pagebuilder/ajax/init');
            return parent::dispatch($request);
        }

        return $this->sendResponse404();
    }
}
