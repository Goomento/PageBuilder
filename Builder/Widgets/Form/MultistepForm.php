<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets\Form;

class MultistepForm extends FormBuilder
{
    /**
     * @inheirtDoc
     */
    const NAME = 'multistep_form';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Multistep Form Builder');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-mail-bulk';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'form', 'builder', 'contact'];
    }

    /**
     * @inheritDoc
     */
    public function isBuildable(): bool
    {
        return false;
    }
}
