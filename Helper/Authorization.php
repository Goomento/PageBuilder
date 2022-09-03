<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Framework\App\Helper\Context;

class Authorization extends AbstractHelper
{
    /**
     * @var AclRetriever
     */
    protected $aclRetriever;

    /**
     * @var array
     */
    protected $currentUserResources;
    /**
     * @var AdminUser
     */
    protected $userHelper;

    /**
     * Authorization constructor.
     * @param Context $context
     * @param AclRetriever $aclRetriever
     * @param AdminUser $userHelper
     */
    public function __construct(
        Context $context,
        AclRetriever $aclRetriever,
        AdminUser $userHelper
    ) {
        parent::__construct($context);
        $this->aclRetriever = $aclRetriever;
        $this->userHelper = $userHelper;
    }

    /**
     * @return array|string[]
     */
    protected function getCurrentUserResources()
    {
        if ($this->currentUserResources === null) {
            $this->currentUserResources = [];
            $user = $this->userHelper->getCurrentAdminUser();
            if ($user && $role = $user->getRole()) {
                $this->currentUserResources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
            }
        }

        return $this->currentUserResources;
    }

    /**
     * @param $resource
     * @return bool
     */
    public function isAllowed($resource)
    {
        if (StateHelper::isCli()) {
            return true;
        }
        $resources = $this->getCurrentUserResources();
        return in_array($resource, $resources) || in_array("Magento_Backend::all", $resources);
    }
}
