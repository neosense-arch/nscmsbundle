<?php

namespace NS\CmsBundle\Block\Settings;

/**
 * Content block settings model
 *
 */
class ContentBlockSettingsModel
{
	/**
	 * @var string
	 */
	private $content;

	/**
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}
}
