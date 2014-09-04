<?php

namespace NS\CmsBundle\Entity;

/**
 * Area entity
 *
 */
class Area
{
	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var boolean
	 */
	private $fixed = false;

    /**
     * @var string
     */
    private $row;

	/**
	 * Creates area from array
	 *
	 * @param  array $data
	 * @return Area
	 * @throws \InvalidArgumentException
	 */
	public static function createFromArray(array $data)
	{
		if (empty($data['name'])) {
			throw new \InvalidArgumentException("Required param 'name' wasn't found");
		}

		$area = new self();
		$area->setName($data['name']);

		if (!empty($data['title'])) {
			$area->setTitle($data['title']);
		}

		if (isset($data['fixed'])) {
			$area->setFixed($data['fixed']);
		}

        if (isset($data['row'])) {
            $area->setRow($data['row']);
        }

		return $area;
	}

	/**
	 * @param  string $title
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
		if (!$this->title) {
			return $this->getName();
		}

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
	 * @return string
	 */
	public function getPath()
	{
		return $this->name;
	}

	/**
	 * @param boolean $fixed
	 */
	public function setFixed($fixed = true)
	{
		$this->fixed = $fixed;
	}

	/**
	 * @return boolean
	 */
	public function isFixed()
	{
		return $this->fixed;
	}

    /**
     * @param string $row
     */
    public function setRow($row)
    {
        $this->row = $row;
    }

    /**
     * @return string
     */
    public function getRow()
    {
        return $this->row;
    }

}
