<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

namespace Goomento\PageBuilder\Block\Widgets;

use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Traits\TraitWidgetBlock;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaFactory;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Goomento\PageBuilder\Block\View\Element\Widget;
use Magento\Store\Model\Store;

class Navigation extends Widget implements DataObject\IdentityInterface
{
    use TraitWidgetBlock;

    /**
     * @var object
     */
    private $menuRepository;

    /**
     * @var object
     */
    private $nodeRepository;

    /**
     * @var mixed
     */
    private $nodeTypeProvider;

    private $nodes;

    private $menu = null;

    /**
     * @var SearchCriteriaFactory
     */
    private $searchCriteriaFactory;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var mixed
     */
    private $templateResolver;

    /**
     * @var mixed
     */
    private $imageFile;

    /**
     * @var string
     */
    private $submenuTemplate;

    /**
     * @var string
     */
    protected $_template = 'Goomento_PageBuilder::widgets/navigation.phtml';

    /**
     * @var string
     */
    protected $baseSubmenuTemplate = 'Snowdog_Menu::menu/sub_menu.phtml';

    /**
     * @var bool
     */
    private $moduleEnabled = false;

    /**
     * @param Template\Context $context
     * @param SearchCriteriaFactory $searchCriteriaFactory
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        SearchCriteriaFactory $searchCriteriaFactory,
        FilterGroupBuilder $filterGroupBuilder,
        array $data = []
    ) {
        if (DataHelper::isModuleOutputEnabled('Snowdog_Menu')) {
            $objectManager = ObjectManager::getInstance();
            $this->moduleEnabled = true;
            $this->menuRepository = $objectManager->get('Snowdog\Menu\Api\MenuRepositoryInterface');
            $this->nodeRepository = $objectManager->get('Snowdog\Menu\Api\NodeRepositoryInterface');
            $this->nodeTypeProvider = $objectManager->get('Snowdog\Menu\Model\NodeTypeProvider');
            $this->imageFile = $objectManager->get('Snowdog\Menu\Model\Menu\Node\Image\File');
            $this->templateResolver = $objectManager->get('Snowdog\Menu\Model\TemplateResolver');
        }

        $this->searchCriteriaFactory = $searchCriteriaFactory;
        $this->filterGroupBuilder = $filterGroupBuilder;
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        if (!$this->moduleEnabled) {
            $this->setTemplate('');
            $this->menu = false;
        } else {
            if (!$this->hasData('menu')) {
                $settings = $this->getSettingsForDisplay();
                $this->setData('menu', $settings['navigation_menu_id'] ?? '');
                $this->setData('menu_type', $settings['navigation_menu_type'] ?? '');
            }
            $this->setTemplate($this->getMenuTemplate($this->_template));
            $this->submenuTemplate = $this->getSubmenuTemplate();
        }
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        $keys = [
            'gmt_navigation_id_' . $this->getData('menu'),
            'gmt_navigation_type_' . $this->getData('menu_type'),
            Block::CACHE_TAG
        ];

        if ($this->getWidget()) {
            $keys['builder_widget'] = $this->getWidget()->getId();
        }

        return $keys;
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function loadMenu()
    {
        if ($this->moduleEnabled && $this->menu === null) {
            $identifier = $this->getData('menu');
            $storeId = $this->_storeManager->getStore()->getId();
            $this->menu = $this->menuRepository->get($identifier, $storeId);

            if (empty($this->menu->getData())) {
                $this->menu = $this->menuRepository->get($identifier, Store::DEFAULT_STORE_ID);
            }
        }

        return $this->menu;
    }

    /**
     * @return mixed|null
     * @throws NoSuchEntityException
     */
    public function getMenu()
    {
        $menu = $this->loadMenu();
        if (!$menu || !$menu->getMenuId()) {
            return null;
        }

        return $menu;
    }

    /**
     * @inheritDoc
     */
    public function getCacheKeyInfo()
    {
        $info = [
            'gmt_navigation',
            'gmt_navigation_id_' . $this->getData('menu'),
            'gmt_navigation_type_' . $this->getData('menu_type'),
        ];

        if ($this->getWidget()) {
            $info['builder_widget'] = $this->getWidget()->getId();
        }

        if ($this->moduleEnabled) {
            $nodeCacheKeyInfo = $this->getNodeCacheKeyInfo();
            if ($nodeCacheKeyInfo) {
                $info = array_merge($info, $nodeCacheKeyInfo);
            }
        }

        return $info;
    }

    /**
     * @return array
     */
    private function getNodeCacheKeyInfo()
    {
        $info = [];
        $nodeType = '';
        $request = $this->getRequest();

        switch ($request->getRouteName()) {
            case 'cms':
                $nodeType = 'cms_page';
                break;
            case 'catalog':
                $nodeType = 'category';
                break;
        }

        $transport = [
            'node_type' => $nodeType,
            'request' => $request
        ];

        $transport = new DataObject($transport);
        $this->_eventManager->dispatch(
            'snowdog_menu_cache_node_type',
            ['transport' => $transport]
        );

        if ($transport->getNodeType()) {
            $nodeType = $transport->getNodeType();
        }

        if ($nodeType) {
            $info = $this->getNodeTypeProvider($nodeType)->getNodeCacheKeyInfo();
        }

        if ($this->getParentNode()) {
            $info[] = 'parent_node_' . $this->getParentNode()->getNodeId();
        }

        return $info;
    }

    /**
     * @param string $nodeType
     * @return bool
     */
    public function isViewAllLinkAllowed($nodeType)
    {
        return $this->getNodeTypeProvider($nodeType)->isViewAllLinkAllowed();
    }

    /**
     * @param mixed $node
     * @return string
     * @throws NoSuchEntityException
     */
    public function renderViewAllLink($node)
    {
        return $this->getMenuNodeBlock($node)
            ->setIsViewAllLink(true)
            ->toHtml();
    }

    /**
     * @param mixed $node
     * @return string
     * @throws NoSuchEntityException
     */
    public function renderMenuNode($node)
    {
        return $this->getMenuNodeBlock($node)->toHtml();
    }

    /**
     * @param array $nodes
     * @param mixed $parentNode
     * @param int $level
     * @return string
     * @throws NoSuchEntityException
     */
    public function renderSubmenu($nodes, $parentNode, $level = 0)
    {
        return $nodes
            ? $this->getSubmenuBlock($nodes, $parentNode, $level)->toHtml()
            : '';
    }

    /**
     * @param int $level
     * @param mixed|null $parent
     * @return array
     */
    public function getNodesTree($level = 0, $parent = null)
    {
        $nodesTree = [];
        $nodes = $this->getNodes($level, $parent);

        foreach ($nodes as $node) {
            $nodesTree[] = [
                'node' => $node,
                'children' => $this->getNodesTree($level + 1, $node)
            ];
        }

        return $nodesTree;
    }

    /**
     * @param string $nodeType
     * @return mixed
     */
    public function getNodeTypeProvider($nodeType)
    {
        return $this->nodeTypeProvider->getProvider($nodeType);
    }

    public function getNodes($level = 0, $parent = null)
    {
        if (empty($this->nodes)) {
            $this->fetchData();
        }
        if (!isset($this->nodes[$level])) {
            return [];
        }
        $parentId = $parent !== null ? $parent['node_id'] : 0;
        if (!isset($this->nodes[$level][$parentId])) {
            return [];
        }
        return $this->nodes[$level][$parentId];
    }

    /**
     * Builds HTML tag attributes from an array of attributes data
     *
     * @param array $array
     * @return string
     */
    public function buildAttrFromArray(array $array)
    {
        $attributes = [];

        foreach ($array as $attribute => $data) {
            if (is_array($data)) {
                $data = implode(' ', $data);
            }

            $attributes[] = $attribute . '="' . $this->getEscaper()->escapeHtml($data) . '"';
        }

        return $attributes ? ' ' . implode(' ', $attributes) : '';
    }

    /**
     * @param string $defaultClass
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMenuCssClass($defaultClass = '')
    {
        $menu = $this->getMenu();

        if ($menu === null) {
            return $defaultClass;
        }

        return $menu->getCssClass();
    }

    /**
     * @param mixed $node
     * @return Template
     * @throws NoSuchEntityException
     */
    private function getMenuNodeBlock($node)
    {
        $nodeBlock = $this->getNodeTypeProvider($node->getType());

        $level = $node->getLevel();
        $isRoot = 0 == $level;
        $nodeBlock->setId($node->getNodeId())
            ->setTitle($node->getTitle())
            ->setLevel($level)
            ->setIsRoot($isRoot)
            ->setIsParent((bool) $node->getIsParent())
            ->setIsViewAllLink(false)
            ->setContent($node->getContent())
            ->setNodeClasses($node->getClasses())
            ->setMenuClass($this->getMenu()->getCssClass())
            ->setMenuCode($this->getData('menu'))
            ->setTarget($node->getTarget())
            ->setImage($node->getImage())
            ->setImageUrl($node->getImage() ? $this->imageFile->getUrl($node->getImage()) : null)
            ->setImageAltText($node->getImageAltText())
            ->setCustomTemplate($node->getNodeTemplate())
            ->setAdditionalData($node->getAdditionalData())
            ->setSelectedItemId($node->getSelectedItemId());

        return $nodeBlock;
    }

    /**
     * @param array $nodes
     * @param mixed $parentNode
     * @param int $level
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function getSubmenuBlock($nodes, $parentNode, $level = 0)
    {
        $block = clone $this;
        $submenuTemplate = $parentNode->getSubmenuTemplate();
        $submenuTemplate = $submenuTemplate
            ? 'Snowdog_Menu::' . $this->getMenu()->getIdentifier() . "/menu/custom/sub_menu/{$submenuTemplate}.phtml"
            : $this->submenuTemplate;

        $block->setSubmenuNodes($nodes)
            ->setParentNode($parentNode)
            ->setLevel($level);

        $block->setTemplateContext($block);
        $block->setTemplate($submenuTemplate);

        return $block;
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     */
    private function fetchData()
    {
        $nodes = $this->nodeRepository->getByMenu($this->loadMenu()->getId());
        $result = [];
        $types = [];
        foreach ($nodes as $node) {
            if (!$node->getIsActive()) {
                continue;
            }

            $level = $node->getLevel();
            $parent = $node->getParentId() ?: 0;
            if (!isset($result[$level])) {
                $result[$level] = [];
            }
            if (!isset($result[$level][$parent])) {
                $result[$level][$parent] = [];
            }
            $result[$level][$parent][] = $node;
            $type = $node->getType();
            if (!isset($types[$type])) {
                $types[$type] = [];
            }
            $types[$type][] = $node;
        }
        $this->nodes = $result;

        foreach ($types as $type => $nodes) {
            $this->nodeTypeProvider->prepareData($type, $nodes);
        }
    }

    private function renderNode($node, $level)
    {
        $type = $node->getType();
        return $this->nodeTypeProvider->render($type, $node->getId(), $level);
    }

    /**
     * @param string $template
     * @return string
     */
    private function getMenuTemplate($template)
    {
        return $this->templateResolver->getMenuTemplate(
            $this,
            $this->getData('menu'),
            $template
        );
    }

    /**
     * @return string
     */
    private function getSubmenuTemplate()
    {
        $baseSubmenuTemplate = $this->baseSubmenuTemplate;
        if ($this->getData('subMenuTemplate')) {
            $baseSubmenuTemplate = $this->getData('subMenuTemplate');
        }

        return $this->getMenuTemplate($baseSubmenuTemplate);
    }
}