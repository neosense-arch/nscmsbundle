<?php

namespace NS\CmsBundle\Manager;

use NS\CmsBundle\Entity\Area;
use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Entity\Template;
use NS\CmsBundle\Entity\TemplateRepository;
use NS\CmsBundle\Service\TemplateLocationService;

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
     * @var TemplateLocationService
     */
    private $templateLocationService;

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
     * Copies template to user app/Resources/views directory
     *
     * @param string $template    template name (i.e. "NSCmsBundle:Blocks:contentBlock.html.twig")
     * @param string $destination template name (i.e. "NSCmsBundle:Blocks:contentBlock_alternative.html.twig")
     */
    public function createUserTemplate($template, $destination = null)
    {
        if (!$destination) {
            // copying to app/Resources dir with the same name
            $destination = $template;
        }

        $templateFileName    = $this->templateLocationService->getVendorTemplateFileName($template);
        $destinationFileName = $this->templateLocationService->getLocalTemplateFileName($destination);

        if ($templateFileName != $destinationFileName && !file_exists($destinationFileName)) {
            @mkdir(dirname($destinationFileName), 0777, true);
            @copy($templateFileName, $destinationFileName);
        }
    }

    /**
     * @return string[]
     */
    public function getAllUserTemplates()
    {
        return $this->templateLocationService->getLocalTemplateNames();
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

    /**
     * @param TemplateLocationService $templateLocationService
     */
    public function setTemplateLocationService(TemplateLocationService $templateLocationService)
    {
        $this->templateLocationService = $templateLocationService;
    }
}
