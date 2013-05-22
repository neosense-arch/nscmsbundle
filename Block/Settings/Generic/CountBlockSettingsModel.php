<?php

namespace NS\CmsBundle\Block\Settings\Generic;

/**
 * Class CountBlockSettingsModel
 * @package NS\CmsBundle\Block\Settings\Generic
 */
class CountBlockSettingsModel
{
	/**
	 * @var string
	 */
	protected $count = 5;

	/**
	 * @param string $count
	 */
	public function setCount($count)
	{
		$this->count = $count;
	}

	/**
	 * @return string
	 */
	public function getCount()
	{
		return $this->count;
	}
}
