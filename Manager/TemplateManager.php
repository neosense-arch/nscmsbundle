<?php

namespace NS\CmsBundle\Manager;

use NS\CmsBundle\Entity\Template;
use NS\CmsBundle\Entity\TemplateRepository;

use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Entity\Area;

/**
 * CMS templates manager
 *
 */
class TemplateManager
{
	/**
	 * @var TemplateRepository
	 */
	private $templateRepository;

	/**
	 * Retrieves area by page and area name
	 *
	 * @param Page $page
	 * @param string $name
	 * @return Area
	 */
	public function getAreaByPageAndName(Page $page, $name)
	{
		$template = $this->getPageTemplate($page);
		return $this->getAreaByTemplateAndName($template, $name);
	}

	/**
	 * Retrieves page template
	 *
	 * @param  Page $page
	 * @return Template
	 * @throws \Exception
	 */
	private function getPageTemplate(Page $page)
	{
		// default page template
		if (!$page->getTemplatePath()) {
			return $this->templateRepository->findDefaultTemplate();
		}

		// searching for template
		$template = $this->templateRepository->findTemplateByPath($page->getTemplatePath());
		if (!$template) {
			throw new \Exception(sprintf(
				"Page #%u template '%s' wasn't found",
				$page->getId(),
				$page->getTemplatePath()
			));
		}

		return $template;
	}

	/**
	 * Retrieves area by template and area name
	 *
	 * @param Template $template
	 * @param string $name
	 * @return Area
	 * @throws \Exception
	 */
	private function getAreaByTemplateAndName(Template $template, $name)
	{
		foreach ($template->getAreas() as $area) {
			if ($area->getName() === $name) {
				return $area;
			}
		}

		throw new \Exception(sprintf(
			"Area '%s' wasn't found in template '%s' ('%s')",
			$name,
			$template->getTitle(),
			$template->getPath()
		));
	}

    /**
     * @param TemplateRepository $templateRepository
     */
    public function setTemplateRepository(TemplateRepository $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }
}
