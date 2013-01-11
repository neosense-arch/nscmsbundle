<?php

namespace NS\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Page form
 *
 */
class PageType extends AbstractType
{
	/**
	 * Builds form
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder
			->add('title', 'text', array(
				'required' => true,
				'label'    => 'Заголовок',
			))
			->add('parent', 'entity', array(
				'label'         => 'Родительская страница',
				'class'         => 'NSCmsBundle:Page',
				'property'      => 'optionLabel',
				'required'      => true,
				'query_builder' => function(EntityRepository $er) {
					$query = $er->createQueryBuilder('c')
						->orderBy('c.root', 'ASC')
						->addOrderBy('c.left', 'ASC')
					;
					return $query;
				},
			))
			->add('name', null, array(
				'required' => false,
				'label'    => 'Имя страницы (лат.)',
			))
        ;
    }

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NS\CmsBundle\Entity\Page'
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_cmsbundle_pagetype';
    }
}
