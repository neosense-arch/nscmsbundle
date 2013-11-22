<?php

namespace NS\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;
use Knp\Menu\NodeInterface;
use NS\SearchBundle\Agent\ModelInterface;

/**
 * Block entity
 *
 * @ORM\Table(name="ns_cms_blocks")
 * @ORM\Entity(repositoryClass="BlockRepository")
 */
class Block implements ModelInterface
{
	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $title;

	/**
	 * @var string
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $settings;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $typeName;

	/**
	 * @var BlockType
	 */
	private $type;

	/**
	 * @var string
	 * @Gedmo\SortableGroup
	 * @ORM\Column(type="string")
	 */
	private $areaName;

	/**
	 * @var Area
	 */
	private $area;

	/**
	 * @var Page
	 * @Gedmo\SortableGroup
	 * @ORM\ManyToOne(targetEntity="Page", inversedBy="blocks")
	 * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $page;

	/**
	 * @var int
	 * @ORM\Column(type="integer")
	 * @Gedmo\SortablePosition
	 */
	private $position;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	private $shared;

	/**
	 * Rendered HTML CAN BE NULL
	 * @var string
	 */
	private $html;

	/**
	 * @return mixed
	 */
	public function getSearchModelId()
	{
		return $this->getId();
	}

	/**
	 * @param string $areaName
	 */
	private function setAreaName($areaName)
	{
		$this->areaName = $areaName;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @param Page $page
	 */
	public function setPage(Page $page)
	{
		$this->page = $page;
	}

	/**
	 * Removes link with page (for site-wide areas)
	 *
	 */
	public function removePage()
	{
		$this->page = null;
	}

	/**
	 * @return Page|null
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * @param int $position
	 */
	public function setPosition($position)
	{
		$this->position = (int)$position;
	}

	/**
	 * @return int
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * @param string $settings
	 */
	public function setSettings($settings)
	{
		$this->settings = $settings;
	}

	/**
	 * @return string
	 */
	public function getSettings()
	{
		return $this->settings;
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
	 * @param string $typeName
	 */
	public function setTypeName($typeName)
	{
		$this->typeName = $typeName;
	}

	/**
	 * @param Area $area
	 */
	public function setArea(Area $area)
	{
		$this->area = $area;
		$this->setAreaName($area->getName());
	}

	/**
	 * @return Area
	 */
	public function getArea()
	{
		return $this->area;
	}

	/**
	 * @return string
	 */
	public function getAreaName()
	{
		return $this->areaName;
	}

	/**
	 * @param BlockType $type
	 */
	public function setType(BlockType $type)
	{
		$this->type = $type;
		$this->setTypeName($type->getName());
	}

	/**
	 * @return BlockType
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getTypeName()
	{
		return $this->typeName;
	}

	/**
	 * @param bool $shared
	 */
	public function setShared($shared = true)
	{
		$this->shared = $shared;
	}

	/**
	 * @return bool
	 */
	public function isShared()
	{
		return $this->shared;
	}

	/**
	 * @param string $html
	 */
	public function setHtml($html)
	{
		$this->html = $html;
	}

	/**
	 * @return string
	 */
	public function getHtml()
	{
		return $this->html;
	}
}
