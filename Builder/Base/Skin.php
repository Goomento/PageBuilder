<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

/**
 * Class Skin
 * @package Goomento\PageBuilder\Builder\Base
 */
abstract class Skin
{

    /**
     * Parent widget.
     *
     * Holds the parent widget of the skin. Default value is null, no parent widget.
     *
     *
     * @var Widget|null
     */
    protected $parent = null;

    /**
     * Skin base constructor.
     *
     * Initializing the skin base class by setting parent widget and registering
     * controls actions.
     *
     * @param Widget $parent
     */
    public function __construct(Widget $parent)
    {
        $this->parent = $parent;

        $this->registerControlsActions();
    }

    /**
     * Get skin ID.
     *
     * Retrieve the skin ID.
     *
     * @abstract
     */
    abstract public function getId();

    /**
     * Get skin title.
     *
     * Retrieve the skin title.
     *
     * @abstract
     */
    abstract public function getTitle();

    /**
     * Render skin.
     *
     * Generates the final HTML on the frontend.
     *
     * @abstract
     */
    abstract public function render();

    /**
     * Register skin controls actions.
     *
     * Run on init and used to register new skins to be injected to the widget.
     * This method is used to register new actions that specify the location of
     * the skin in the widget.
     *
     */
    protected function registerControlsActions()
    {
    }

    /**
     * Get skin control ID.
     *
     * Retrieve the skin control ID. Note that skin controls have special prefix
     * to distinguish them from regular controls, and from controls in other
     * skins.
     *
     *
     * @param string $control_base_id Control base ID.
     *
     * @return string Control ID.
     */
    protected function getControlId($control_base_id)
    {
        $skin_id = str_replace('-', '_', $this->getId());
        return $skin_id . '_' . $control_base_id;
    }

    /**
     * Get skin settings.
     *
     * Retrieve all the skin settings or, when requested, a specific setting.
     *
     * @TODO: rename to get_setting() and create backward compatibility.
     *
     *
     * @param string $control_base_id Control base ID.
     *
     * @return Widget Widget instance.
     */
    public function getInstanceValue($control_base_id)
    {
        $control_id = $this->getControlId($control_base_id);
        return $this->parent->getSettings($control_id);
    }

    /**
     * Start skin controls section.
     *
     * Used to add a new section of controls to the skin.
     *
     *
     * @param string $id   Section ID.
     * @param array  $args Section arguments.
     */
    public function startControlsSection($id, $args)
    {
        $args['condition']['_skin'] = $this->getId();
        $this->parent->startControlsSection($this->getControlId($id), $args);
    }

    /**
     * End skin controls section.
     *
     * Used to close an existing open skin controls section.
     *
     */
    public function endControlsSection()
    {
        $this->parent->endControlsSection();
    }

    /**
     * Add new skin control.
     *
     * Register a single control to the allow the user to set/update skin data.
     *
     *
     * @param string $id   Control ID.
     * @param array  $args Control arguments.
     *
     * @return bool True if skin added, False otherwise.
     */
    public function addControl($id, $args)
    {
        $args['condition']['_skin'] = $this->getId();
        return $this->parent->addControl($this->getControlId($id), $args);
    }

    /**
     * Update skin control.
     *
     * Change the value of an existing skin control.
     *
     *
     *
     * @param string $id      Control ID.
     * @param array  $args    Control arguments. Only the new fields you want to update.
     * @param array  $options Optional. Some additional options.
     */
    public function updateControl($id, $args, array $options = [])
    {
        $args['condition']['_skin'] = $this->getId();
        $this->parent->updateControl($this->getControlId($id), $args, $options);
    }

    /**
     * Remove skin control.
     *
     * Unregister an existing skin control.
     *
     *
     * @param string $id Control ID.
     */
    public function removeControl($id)
    {
        $this->parent->removeControl($this->getControlId($id));
    }

    /**
     * Add new responsive skin control.
     *
     * Register a set of controls to allow editing based on user screen size.
     *
     *
     * @param string $id   Responsive control ID.
     * @param array  $args Responsive control arguments.
     */
    public function addsResponsiveControl($id, $args)
    {
        $args['condition']['_skin'] = $this->getId();
        $this->parent->addResponsiveControl($this->getControlId($id), $args);
    }

    /**
     * Update responsive skin control.
     *
     * Change the value of an existing responsive skin control.
     *
     *
     * @param string $id   Responsive control ID.
     * @param array  $args Responsive control arguments.
     */
    public function updateResponsiveControl($id, $args)
    {
        $this->parent->updateResponsiveControl($this->getControlId($id), $args);
    }

    /**
     * Remove responsive skin control.
     *
     * Unregister an existing skin responsive control.
     *
     *
     * @param string $id Responsive control ID.
     */
    public function removeResponsiveControl($id)
    {
        $this->parent->removeResponsiveControl($this->getControlId($id));
    }

    /**
     * Start skin controls tab.
     *
     * Used to add a new tab inside a group of tabs.
     *
     *
     * @param string $id   Control ID.
     * @param array  $args Control arguments.
     */
    public function startControlsTab($id, $args)
    {
        $args['condition']['_skin'] = $this->getId();
        $this->parent->startControlsTab($this->getControlId($id), $args);
    }

    /**
     * End skin controls tab.
     *
     * Used to close an existing open controls tab.
     *
     */
    public function endControlsTab()
    {
        $this->parent->endControlsTab();
    }

    /**
     * Start skin controls tabs.
     *
     * Used to add a new set of tabs inside a section.
     *
     *
     * @param string $id Control ID.
     * @throws \Exception
     */
    public function startControlsTabs($id)
    {
        $this->parent->startControlsTabs($this->getControlId($id));
    }

    /**
     * End skin controls tabs.
     *
     * Used to close an existing open controls tabs.
     *
     */
    public function endControlsTabs()
    {
        $this->parent->endControlsTabs();
    }

    /**
     * Add new group control.
     *
     * Register a set of related controls grouped together as a single unified
     * control.
     *
     *
     * @param string $group_name Group control name.
     * @param array  $args       Group control arguments. Default is an empty array.
     */
    final public function addGroupControl($group_name, $args = [])
    {
        $args['name'] = $this->getControlId($args['name']);
        $args['condition']['_skin'] = $this->getId();
        $this->parent->addGroupControl($group_name, $args);
    }

    /**
     * Set parent widget.
     *
     * Used to define the parent widget of the skin.
     *
     *
     * @param Widget $parent Parent widget.
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }
}
