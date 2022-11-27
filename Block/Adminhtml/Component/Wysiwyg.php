<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Adminhtml\Component;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;

class Wysiwyg extends Widget
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/form.phtml';
    /**
     * @var FormFactory
     */
    private $formFactory;
    /**
     * @var Config
     */
    private $wysiwygConfig;

    /**
     * @param Context $context
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    /**
     * Prepare form.
     * Adding editor field to render
     *
     */
    protected function _prepareForm()
    {
        /** @var Form $form */
        $form = $this->formFactory->create(
            [
                'data' => ['id' => 'wysiwyg_edit_form', 'action' => $this->getData('action'), 'method' => 'post'],
            ]
        );

        $config['document_base_url'] = $this->getData('document_base_url');
        $config['store_id']          = $this->getData('store_id');
        $config['add_variables']     = true;
        $config['add_widgets']       = true;
        $config['add_directives']    = true;
        $config['use_container']     = true;
        $config['container_class']   = 'hor-scroll';

        $form->addField(
            $this->getData('element_id'),
            'editor',
            [
                'name' => 'content',
                'style' => 'width:725px;height:460px',
                'required' => true,
                'force_load' => true,
                'use_origin_editor' => true,
                'config' => $this->wysiwygConfig->getConfig($config)
            ]
        );

        $this->setForm($form);
        return $this;
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        if (is_object($this->getForm())) {
            return $this->getForm()->getHtml();
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        $this->_prepareForm();
        return parent::_beforeToHtml();
    }
}
