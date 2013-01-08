<?php

namespace NS\CmsBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Yaml\Yaml;

/**
 * Templates repository
 *
 */
class TemplateRepository extends ContainerAware
{
	/**
	 * Templates filename
	 * @var string
	 */
	const TEMPLATES_CONFIG_FILENAME = 'ns_cms.templates.yml';

	/**
	 * Retrieves all templates
	 *
	 * @throws \Exception
	 * @return Template[]
	 */
	public function findAll()
	{
		/**
		 * adding bundles' templates
		 * @var $bundle Bundle
		 */
		$paths = array();
		foreach ($this->getKernel()->getBundles() as $bundle) {
			$paths[] = $bundle->getPath() . '/Resources/config';
		}

		// adding config templates
		$paths[] = $this->getKernel()->getRootDir() . '/config';

		// adding templates
		$templates = array();
		foreach ($paths as $path) {
			$fileName = $path . '/' . self::TEMPLATES_CONFIG_FILENAME;
			if (file_exists($fileName)) {
				$templates = array_merge($templates, $this->createTemplatesFromYml($fileName));
			}
		}

		return array_values($templates);
	}

	/**
	 * Retrieves template by path
	 *
	 * @param  string $path
	 * @return Template|null
	 */
	public function findTemplateByPath($path)
	{
		foreach ($this->findAll() as $template) {
			if ($template->getPath() === $path) {
				return $template;
			}
		}
		return null;
	}

	/**
	 * Retrieves default template
	 *
	 * @return Template
	 * @throws \Exception
	 */
	public function findDefaultTemplate()
	{
		$templates = $this->findAll();

		if (!count($templates)) {
			throw new \Exception("Templates wasn't found");
		}

		foreach ($templates as $template) {
			if ($template->isDefault()) {
				return $template;
			}
		}

		return $templates[0];
	}

	/**
	 * Creates templates from YAML file
	 *
	 * @param  string $fileName
	 * @return Template[]
	 * @throws \Exception
	 */
	private function createTemplatesFromYml($fileName)
	{
		$templates = array();

		$yml = file_get_contents($fileName);
		foreach (Yaml::parse($yml) as $data) {
			if (!is_array($data)) {
				throw new \Exception("Config section 'templates' must be array in '{$fileName}'");
			}

			// adding templates
			foreach ($data as $row) {
				$template = $this->createTemplateFromArray($row);
				$templates[$template->getPath()] = $template;
			}
		}

		return $templates;
	}

	/**
	 * Creates template object from config array row
	 *
	 * @param  array $data
	 * @return Template
	 * @throws \Exception
	 */
	private function createTemplateFromArray(array $data)
	{
		if (empty($data['path'])) {
			throw new \Exception("Required option 'path' wasn't found");
		}

		$template = new Template();
		$template->setPath($data['path']);
		if (!empty($data['title'])) {
			$template->setTitle($data['title']);
		}
		if (isset($data['default'])) {
			$template->setDefault($data['default']);
		}

		// areas
		if (!empty($data['areas']) && is_array($data['areas'])) {
			$areas = array();
			foreach ($data['areas'] as $area) {
				$areas[] = Area::createFromArray($area);
			}
			$template->setAreas($areas);
		}

		return $template;
	}

	/**
	 * @return Kernel
	 */
	private function getKernel()
	{
		return $this->container->get('kernel');
	}
}
