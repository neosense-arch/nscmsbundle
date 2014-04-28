<?php

namespace NS\CmsBundle\Block\Settings;

/**
 * Menu block settings model
 *
 */
class MenuBlockSettingsModel
{
	/**
	 * @var int
	 */
	private $rootPageId;

	/**
	 * @var int
	 */
	private $depth = 0;

    /**
     * @var string
     */
    private $skip = '';

    /**
     * @var bool
     */
    private $isSubmenu = false;

	/**
	 * @param int $rootPageId
	 */
	public function setRootPageId($rootPageId)
	{
		$this->rootPageId = $rootPageId;
	}

	/**
	 * @return int
	 */
	public function getRootPageId()
	{
		return $this->rootPageId;
	}

	/**
	 * @param int $depth
	 */
	public function setDepth($depth)
	{
		$this->depth = $depth;
	}

	/**
	 * @return int
	 */
	public function getDepth()
	{
		return $this->depth;
	}

    /**
     * @param string $skip
     */
    public function setSkip($skip)
    {
        $this->skip = $skip;
    }

    /**
     * @return string
     */
    public function getSkip()
    {
        return $this->skip;
    }

    /**
     * @return array
     */
    public function getSkipArray()
    {
        return explode(',', $this->skip);
    }

    /**
     * @param boolean $isSubmenu
     */
    public function setIsSubmenu($isSubmenu)
    {
        $this->isSubmenu = $isSubmenu;
    }

    /**
     * @return boolean
     */
    public function getIsSubmenu()
    {
        return $this->isSubmenu;
    }
}
