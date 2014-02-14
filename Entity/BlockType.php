<?php

namespace NS\CmsBundle\Entity;

use Symfony\Component\HttpKernel\Bundle\Bundle;

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
	 * @var Bundle
	 */
	private $bundle;

	/**
	 * @var array
	 */
	private $raw;

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

		$block = new self();
		$block->setName($data['name']);
		$block->setTitle($data['name']);

		if (!empty($data['title'])) {
			$block->setTitle($data['title']);
		}
		if (!empty($data['template'])) {
			$block->setTemplate($data['template']);
		}
		if (!empty($data['settingsFormClass'])) {
			$block->setSettingFormClass($data['settingsFormClass']);
		}
		if (!empty($data['settingsModelClass'])) {
			$block->setSettingsModelClass($data['settingsModelClass']);
		}

		$block->raw = $data;

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
     * Retrieves block template
     * Example: "NSCmsBundle:Blocks:menuBlock.html.twig"
     *
	 * @return string
	 */
	public function getTemplate()
	{
        if (!$this->template) {
            return $this->getName() . '.html.twig';
        }
		return $this->template;
	}

    /**
     * Retrieves template file name
     * Example: "menuBlock.html.twig"
     *
     * @return string
     */
    public function getTemplateFileName()
    {
        return array_pop(explode(':', $this->getTemplate()));
    }

    /**
     * Retrieves template file full path
     * Example: "/project/vendor/neosense/cms-bundle/NS/CmsBundle/Resources/views/Blocks/menuBlock.html.twig"
     *
     * @return string
     */
    public function getTemplateFilePath()
    {
        return sprintf(
            '%s/Resources/views/Blocks/%s',
            $this->getBundle()->getPath(),
            $this->getTemplateFileName()
        );
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
	/**
	 * @param Bundle $bundle
	 */
	public function setBundle(Bundle $bundle)
	{
		$this->bundle = $bundle;
	}
	/**
	 * @return Bundle
	 */
	public function getBundle()
	{
		return $this->bundle;
	}

	/**
	 * @return array
	 */
	public function getRaw()
	{
		return $this->raw;
	}

	/**
	 * @param array $raw
	 */
	public function setRaw(array $raw)
	{
		$this->raw = $raw;
	}

	/**
	 * @return mixed
	 * @throws \Exception
	 */
	public function createSettingsModel()
	{
		$className = $this->getSettingsModelClass();
		if (!class_exists($className)) {
			throw new \Exception("Settings model class '{$className}' wasn't found");
		}
		return new $className();
	}

	/**
	 * @return mixed
	 * @throws \Exception
	 */
	public function createSettingsFormType()
	{
		$className = $this->getSettingFormClass();
		if (!class_exists($className)) {
			throw new \Exception("Settings form class '{$className}' wasn't found");
		}
		return new $className();
	}

}
