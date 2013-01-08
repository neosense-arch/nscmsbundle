<?php

namespace NS\CmsBundle\Entity;

/**
 * Template entity
 *
 */
class Template
{
	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var boolean
	 */
	private $default = false;

	/**
	 * @var Area[]
	 */
	private $areas = array();

	/**
	 * @param  string $title
	 * @return Template
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		if (!$this->title) {
			return $this->getName();
		}

		return $this->title;
	}

	/**
	 * @param  string $path
	 * @return Template
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @param boolean $default
	 */
	public function setDefault($default = true)
	{
		$this->default = $default;
	}

	/**
	 * @return boolean
	 */
	public function isDefault()
	{
		return $this->default;
	}

	/**
	 * Retrieves base template name
	 *
	 * @return string
	 */
	public function getName()
	{
		return trim(basename($this->getPath(), '.phtml.twig'), ':');
	}

	/**
	 * @param Area[] $areas
	 */
	public function setAreas(array $areas)
	{
		$this->areas = $areas;
	}

	/**
	 * @return Area[]
	 */
	public function getAreas()
	{
		return $this->areas;
	}
}
