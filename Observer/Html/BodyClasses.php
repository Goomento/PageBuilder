<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Observer\Html;

use Goomento\PageBuilder\Helper\Hooks;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Page\Config;

/**
 * Class BodyClasses
 * @package Goomento\PageBuilder\Observer\Html
 */
class BodyClasses implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $pageConfig;

    /**
     * BodyClasses constructor.
     * @param Config $pageConfig
     */
    public function __construct(
        Config $pageConfig
    ) {
        $this->pageConfig = $pageConfig;
    }

    /**
     * @param Observer $observer
     * @return BodyClasses
     */
    public function execute(Observer $observer)
    {
        $bodyClasses = Hooks::applyFilters('body_class', []);
        if ($bodyClasses) {
            foreach ($bodyClasses as $class) {
                $this->pageConfig->addBodyClass($class);
            }
        }

        return $this;
    }
}
