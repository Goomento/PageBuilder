<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Ajax;

use Goomento\PageBuilder\Helper\HooksHelper;

class Heartbeat extends Json
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->setResponseData(
            HooksHelper::applyFilters('pagebuilder/heartbeat/response', [])->getResult()
        )->sendResponse();
    }
}
