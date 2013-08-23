<?php

namespace NS\CmsBundle\Form\Element;

use NS\CmsBundle\Form\ChoiceList\PageChoiceList;
use NS\CmsBundle\Service\PageService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PageSelectType
 *
 * @package NS\CmsBundle\Form\Element
 */
class PageSelectType extends AbstractType
{
	/**
	 * @var PageService
	 */
	private $pageService;

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'choice_list' => new PageChoiceList($this->pageService->getRootPage()),
		));
	}

	/**
	 * Returns the name of this type.
	 *
	 * @return string The name of this type
	 */
	public function getName()
	{
		return 'ns_cms_form_element_page_select_type';
	}

	/**
	 * @return string
	 */
	public function getParent()
	{
		return 'choice';
	}
	/**
	 * @param PageService $pageService
	 */
	public function setPageService(PageService $pageService)
	{
		$this->pageService = $pageService;
	}
}