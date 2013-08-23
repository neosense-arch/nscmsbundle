<?php

namespace NS\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Page entity
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="ns_cms_pages")
 * @ORM\Entity(repositoryClass="PageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Page
{
	const MAIN_PAGE_NAME  = 'main';
	const MAIN_PAGE_TITLE = 'Главная';
	const ROOT_PAGE_NAME  = 'ns_cms_pages_root_page';
	const ROOT_PAGE_TITLE = 'ns_cms_pages_root_page';

	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @Gedmo\TreeLeft
	 * @ORM\Column(name="t_left", type="integer")
	 */
	private $left;

	/**
	 * @Gedmo\TreeLevel
	 * @ORM\Column(name="t_level", type="integer")
	 */
	private $level;

	/**
	 * @Gedmo\TreeRight
	 * @ORM\Column(name="t_right", type="integer")
	 */
	private $right;

	/**
	 * @Gedmo\TreeRoot
	 * @ORM\Column(name="t_root", type="integer", nullable=true)
	 */
	private $root;

	/**
	 * @var Page
	 *
	 * @Gedmo\TreeParent
	 * @Gedmo\SortableGroup
	 * @ORM\ManyToOne(targetEntity="Page", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $parent;

	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="Page", mappedBy="parent")
	 * @ORM\OrderBy({"left" = "ASC"})
	 */
	private $children;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 */
	private $title;

	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $name;

	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $templatePath;

	/**
	 * @var int
	 * @ORM\Column(type="integer")
	 * @Gedmo\SortablePosition
	 */
	private $position;

	/**
	 * @var Block[]
	 * @ORM\OneToMany(targetEntity="Block", mappedBy="page")
	 * @ORM\OrderBy({"areaName" = "ASC", "position" = "ASC"})
	 */
	private $blocks;

	/**
	 * @ORM\Column(type="boolean")
	 * @var boolean
	 */
	private $visible = true;

	/**
	 * @param ArrayCollection $children
	 */
	public function setChildren($children)
	{
		$this->children = $children;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * @return bool
	 */
	public function hasChildren()
	{
		return $this->children->count() > 0;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param Page $parent
	 */
	public function setParent(Page $parent)
	{
		$this->parent = $parent;
	}

	/**
	 * @return Page
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $path
	 */
	public function setTemplatePath($path)
	{
		$this->templatePath = $path;
	}

	/**
	 * @return string
	 */
	public function getTemplatePath()
	{
		return $this->templatePath;
	}

	/**
	 * Retrieves page tree level
	 *
	 * @return int
	 */
	public function getLevel()
	{
		return $this->level;
	}

	/**
	 * Retrieves options label (for combobox)
	 *
	 * @param  string $levelIndicator
	 * @return string
	 */
	public function getOptionLabel($levelIndicator = '--')
	{
		$title = $this->getTitle();
		if (!$this->getParent()) {
			$title = '[ Не выбрано ]';
		}

		return str_repeat($levelIndicator, $this->level) . ' ' . $title;
	}

	/**
	 * @param int $position
	 */
	public function setPosition($position)
	{
		$this->position = $position;
	}

	/**
	 * @return int
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * @param $blocks
	 */
	public function setBlocks($blocks)
	{
		$this->blocks = $blocks;
	}

	/**
	 * @return Block[]
	 */
	public function getBlocks()
	{
		return $this->blocks;
	}

	/**
	 * @param boolean $visible
	 */
	public function setVisible($visible =  true)
	{
		$this->visible = $visible;
	}

	/**
	 * @return boolean
	 */
	public function isVisible()
	{
		return $this->visible;
	}

	/**
	 * @return bool
	 */
	public function isMain()
	{
		return $this->getName() === self::MAIN_PAGE_NAME;
	}
}
