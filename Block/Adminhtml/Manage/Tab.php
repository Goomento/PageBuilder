<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Manage;

use Magento\Backend\Block\Template;

class Tab extends Template
{
    const TAB_TITLE = 'title';

    const TAB_URL = 'url';

    const TAB_POST_URL = 'post_url';

    const TAB_DESCRIPTION = 'description';

    const BUTTON_LABEL = 'button_label';

    /**
     * Title of tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return (string) $this->getData(self::TAB_TITLE);
    }

    /**
     * Description of tab
     *
     * @return string
     */
    public function getTabDescription()
    {
        return (string) $this->getData(self::TAB_DESCRIPTION);
    }

    /**
     * Submit button label
     *
     * @return string
     */
    public function getButtonLabel()
    {
        return $this->getData(self::BUTTON_LABEL) ?: __('Save');
    }

    /**
     * Get URL of tab
     *
     * @return string
     */
    public function getTabUrl()
    {
        $url = $this->getData(self::TAB_URL);
        if (trim($url)) {
            $url = $this->_urlBuilder->getUrl($url);
        }

        return $url;
    }

    /**
     * Get URL for posting data
     *
     * @return string
     */
    public function getTabPostUrl()
    {
        $url = $this->getData(self::TAB_POST_URL);
        if (trim($url)) {
            $url = $this->_urlBuilder->getUrl($url);
        }

        return $url;
    }
}
