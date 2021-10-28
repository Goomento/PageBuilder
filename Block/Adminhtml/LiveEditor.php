<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml;

use Magento\Backend\Block\Template;

class LiveEditor extends Template
{
    /**
     * @inheridoc
     */
    protected $_template = 'Goomento_Pagebuilder::live_editor.phtml';

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->_urlBuilder->getUrl('pagebuilder/ajax/action');
    }
}
