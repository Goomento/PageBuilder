<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Model\Layout\Update\ValidatorFactory;

/**
 * Class PostDataProcessor
 * @package Goomento\PageBuilder\Controller\Adminhtml\Page
 */
class ContentDataProcessor
{
    /**
     * @var Date
     */
    protected $dateFilter;

    /**
     * @var ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Date $dateFilter
     * @param ManagerInterface $messageManager
     * @param ValidatorFactory $validatorFactory
     */
    public function __construct(
        Date $dateFilter,
        ManagerInterface $messageManager,
        ValidatorFactory $validatorFactory
    ) {
        $this->dateFilter = $dateFilter;
        $this->messageManager = $messageManager;
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array $data
     * @return array
     */
    public function filter($data)
    {
        $filterRules = [];
        return (new \Zend_Filter_Input($filterRules, [], $data))->getUnescaped();
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool     Return FALSE if some item is invalid
     */
    public function validate($data): bool
    {
        return $this->validateRequireEntry($data);
    }

    /**
     * Check if required fields is not empty
     *
     * @param array $data
     * @return bool
     */
    public function validateRequireEntry(array $data): bool
    {
        $requiredFields = [
            'title' => __('Content Title'),
            'type' => __('Content Type'),
            'stores' => __('Content View'),
            'status' => __('Status')
        ];
        $errorNo = true;
        foreach ($data as $field => $value) {
            if (in_array($field, array_keys($requiredFields)) && $value == '') {
                $errorNo = false;
                $this->messageManager->addErrorMessage(
                    __('To apply changes you should fill in hidden required "%1" field', $requiredFields[$field])
                );
            }
        }
        return $errorNo;
    }
}
