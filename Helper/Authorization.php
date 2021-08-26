<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Framework\App\Helper\Context;

/**
 * Class Authorization
 * @package Goomento\PageBuilder\Helper
 */
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
     * @var UserHelper
     */
    protected $userHelper;

    /**
     * Authorization constructor.
     * @param Context $context
     * @param AclRetriever $aclRetriever
     * @param UserHelper $userHelper
     */
    public function __construct(
        Context $context,
        AclRetriever $aclRetriever,
        UserHelper $userHelper
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
        if (is_null($this->currentUserResources)) {
            $user = $this->userHelper->getCurrentAdminUser();
            $role = $user->getRole();
            $this->currentUserResources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
        }

        return $this->currentUserResources;
    }

    /**
     * @param $resource
     * @return bool
     */
    public function isAllowed($resource)
    {
        if (StaticUtils::isCli()) {
            return true;
        }
        $resources = $this->getCurrentUserResources();
        return in_array($resource, $resources) || in_array("Magento_Backend::all", $resources);
    }
}
