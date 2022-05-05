<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\Framework\Data\Form\Element;

use Goomento\PageBuilder\Helper\Data;
use Goomento\PageBuilder\Helper\EscaperHelper;
use Magento\Framework\Data\Form\Element\Editor as FormEditor;
use Magento\Framework\UrlInterface;

class Editor
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @param Data $dataHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Data $dataHelper,
        UrlInterface $urlBuilder
    )
    {
        $this->dataHelper = $dataHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param FormEditor $subject
     * @param callable $proceed
     * @return string|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function aroundGetElementHtml(
        FormEditor $subject,
        callable $proceed
    )
    {
        if (!$this->dataHelper->isBuilderAssistanceActive() ||
            $subject->getData('use_origin_editor') === true) {
            return $proceed();
        }

        $name = $subject->getName();
        $componentName = EscaperHelper::slugify($name);
        $jsParams = [
            '*' => [
                'Magento_Ui/js/core/app' => [
                    'components' => [
                        $componentName => [
                            'component' => 'Goomento_PageBuilder/js/ui/form/element/builderAssistance',
                            'endpoint' => $this->urlBuilder->getUrl('pagebuilder/ajax/BuilderAssistance'),
                            'value' => $subject->getValue(),
                            'html_name' => $name,
                        ]
                    ]
                ]
            ]
        ];
        $js = '<script type="text/x-magento-init">' . \Zend_Json::encode($jsParams) . '</script>';
        return '<div data-bind="scope: \'' . $componentName . '\'"><!-- ko template: getTemplate() --><!-- /ko --></div>' . $js;
    }
}
