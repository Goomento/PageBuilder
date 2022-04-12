<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Manage;

use Magento\Backend\Block\Template;

class Tabs extends Template
{
    /**
     * @return Tab[]
     */
    public function getTabs()
    {
        $tabNames = (array) $this->getChildNames();
        $tabs = [];
        if (!empty($tabNames)) {
            foreach ($tabNames as &$tab) {
                $tab = $this->getChildBlock($tab);
                if ($tab instanceof Tab) {
                    $tabs[] = $tab;
                }
            }
        }

        return $tabs;
    }
}
