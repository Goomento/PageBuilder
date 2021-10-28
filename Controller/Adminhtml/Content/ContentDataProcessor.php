<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Magento\Framework\Message\ManagerInterface;

class ContentDataProcessor
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ManagerInterface $messageManager
    ) {
        $this->messageManager = $messageManager;
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool     Return FALSE if some item is invalid
     */
    public function validate(array $data): bool
    {
        return $this->validateRequireEntry($data);
    }

    /**
     * Check if required fields is not empty
     *
     * @param array $data
     * @return bool
     */
    private function validateRequireEntry(array $data): bool
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
