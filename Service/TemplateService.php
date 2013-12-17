<?php

namespace NS\CmsBundle\Service;

use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Entity\Template;
use NS\CmsBundle\Entity\TemplateRepository;

/**
 * Class TemplateService
 *
 * @package NS\CmsBundle\Service
 */
class TemplateService
{
	/**
	 * @var TemplateRepository
	 */
	private $templateRepository;

	/**
	 * Retrieves page template
	 *
	 * @param Page $page
	 * @return Template
	 * @throws \Exception
	 */
	public function getPageTemplate(Page $page)
	{
		$path = $page->getTemplatePath();
		if (!$path) {
			return $this
				->templateRepository
				->findDefaultTemplate();
		}

		$template = $this
			->templateRepository
			->findTemplateByPath($path);

		if (!$template) {
			throw new \Exception("Template '{$path}' wasn't found");
		}

		return $template;
	}

	/**
	 * @param TemplateRepository $templateRepository
	 */
	public function setTemplateRepository(TemplateRepository $templateRepository)
	{
		$this->templateRepository = $templateRepository;
	}
} 