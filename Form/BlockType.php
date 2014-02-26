<?php

namespace NS\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Block form
 *
 */
class BlockType extends AbstractType
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
			->add('template', 'text', array(
				'required' => false,
				'label'    => 'Шаблон',
			))
			->add('useCache', 'checkbox', array(
				'required' => false,
				'label'    => 'Кэшировать блок',
			))
        ;
    }

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NS\CmsBundle\Entity\Block'
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_cmsbundle_blocktype';
    }
}
