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
}
