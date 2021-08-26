<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Goomento\Core\Traits\TraitStaticInstances;

/**
 * Class Events
 * @package Goomento\PageBuilder\Helper
 */
class Events extends AbstractHelper
{
    use TraitStaticInstances;

    /**
     * @param $eventName
     * @param array $data
     */
    public static function dispatch($eventName, array $data = [])
    {
        /** @var Events $instance */
        $instance = self::getInstance();
        $instance->_eventManager->dispatch($eventName, $data = []);
    }
}
