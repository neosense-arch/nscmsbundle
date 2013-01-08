<?php

namespace NS\CmsBundle\Entity;

/**
 * Block entity
 *
 */
class BlockType
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $formName;

	/**
	 * @var string
	 */
	private $template;

	/**
	 * @var string
	 */
	private $settingFormClass;

	/**
	 * @var string
	 */
	private $settingsModelClass;

	/**
	 * Creates block from array
	 *
	 * @param  array $data
	 * @return BlockType
	 * @throws \InvalidArgumentException
	 */
	public static function createFromArray(array $data)
	{
		if (empty($data['name'])) {
			throw new \InvalidArgumentException("Required param 'name' wasn't found");
		}
		if (empty($data['title'])) {
			throw new \InvalidArgumentException("Required param 'title' wasn't found");
		}
		if (empty($data['form'])) {
			throw new \InvalidArgumentException("Required param 'form' wasn't found");
		}
		if (empty($data['template'])) {
			throw new \InvalidArgumentException("Required param 'template' wasn't found");
		}
		if (empty($data['settingsFormClass'])) {
			throw new \InvalidArgumentException("Required param 'settingsFormClass' wasn't found");
		}
		if (empty($data['settingsModelClass'])) {
			throw new \InvalidArgumentException("Required param 'settingsModelClass' wasn't found");
		}

		$block = new self();
		$block->setName($data['name']);
		$block->setTitle($data['title']);
		$block->setFormName($data['form']);
		$block->setTemplate($data['template']);
		$block->setSettingFormClass($data['settingsFormClass']);
		$block->setSettingsModelClass($data['settingsModelClass']);

		return $block;
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
	 * @param string $formName
	 */
	public function setFormName($formName)
	{
		$this->formName = $formName;
	}

	/**
	 * @return string
	 */
	public function getFormName()
	{
		return $this->formName;
	}

	/**
	 * @param string $template
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
	}

	/**
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * @param string $settingFormClass
	 */
	public function setSettingFormClass($settingFormClass)
	{
		$this->settingFormClass = $settingFormClass;
	}

	/**
	 * @return string
	 */
	public function getSettingFormClass()
	{
		return $this->settingFormClass;
	}

	/**
	 * @param string $settingsModelClass
	 */
	public function setSettingsModelClass($settingsModelClass)
	{
		$this->settingsModelClass = $settingsModelClass;
	}

	/**
	 * @return string
	 */
	public function getSettingsModelClass()
	{
		return $this->settingsModelClass;
	}

}
