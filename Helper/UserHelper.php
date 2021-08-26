<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Helper\Context;

/**
 * Class UserHelper
 * @package Goomento\PageBuilder\Helper
 */
class UserHelper extends AbstractHelper
{
    /**
     * @var Session
     */
    protected $authSession;

    /**
     * AdminUserHelper constructor.
     * @param Context $context
     * @param Session $authSession
     */
    public function __construct(
        Context $context,
        Session $authSession
    ) {
        parent::__construct($context);
        $this->authSession = $authSession;
    }

    /**
     * @return \Magento\User\Model\User|null
     */
    public function getCurrentAdminUser()
    {
        return $this->authSession->getUser();
    }
}
