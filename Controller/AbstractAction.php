<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller;

use Goomento\Core\Model\Registry;
use Goomento\PageBuilder\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Goomento\PageBuilder\Logger\Logger;

abstract class AbstractAction extends Action
{
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * AbstractAction constructor.
     * @param Context $context
     * @param Logger $logger
     * @param Registry $registry
     * @param Data $dataHelper
     */
    public function __construct(
        Context $context,
        Logger $logger,
        Registry $registry,
        Data $dataHelper
    ) {
        $this->logger = $logger;
        $this->registry = $registry;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }
}
