<?php

namespace NS\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Page additional settings form
 *
 */
class PageAdditionalSettingsType extends AbstractType
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
			->add('name', null, array(
				'label'    => 'Имя страницы (лат.)',
				'required' => false,
			))
			->add('parent', 'entity', array(
				'label'         => 'Родительская страница',
				'required'      => true,
				'class'         => 'NSCmsBundle:Page',
				'property'      => 'optionLabel',
				'query_builder' => function(EntityRepository $er) {
					$query = $er->createQueryBuilder('c')
						->orderBy('c.root', 'ASC')
						->addOrderBy('c.left', 'ASC')
					;
					return $query;
				},
			))
			->add('templatePath', 'ns_cmsbundle_templatepathtype', array(
				'label'       => 'Шаблон',
				'required'    => false,
				'empty_value' => '[ Не выбрано ]',
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
        return 'ns_cmsbundle_pageadditionalsettingstype';
    }
}
