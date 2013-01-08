<?php

namespace NS\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use NS\CmsBundle\Entity\TemplateRepository;

/**
 * Template choice type
 *
 */
class TemplatePathType extends AbstractType
{
	/**
	 * @var TemplateRepository
	 */
	private $templateRepository;

	/**
	 * Sets template repository
	 *
	 * @param TemplateRepository $templateRepository
	 */
	public function setTemplateRepository(TemplateRepository $templateRepository)
	{
		$this->templateRepository = $templateRepository;
	}

	/**
	 * @return string
	 */
	public function getParent()
	{
		return 'choice';
	}

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'choices'           => $this->getChoices(),
		));
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'ns_cmsbundle_templatepathtype';
	}

	/**
	 * Retrieves choices
	 *
	 * @return string[]
	 */
	private function getChoices()
	{
		$choices = array();

		foreach ($this->getTemplateRepository()->findAll() as $template) {
			$choices[$template->getPath()] = $template->getTitle();
		}

		return $choices;
	}

	/**
	 * @return TemplateRepository
	 */
	private function getTemplateRepository()
	{
		return $this->templateRepository;
	}
}
