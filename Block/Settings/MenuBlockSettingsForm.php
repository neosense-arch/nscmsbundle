<?php

namespace NS\CmsBundle\Block\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Menu block settings form
 *
 */
class MenuBlockSettingsForm extends AbstractType
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
			->add('rootPageId', 'ns_cms_form_element_page_select_type', array(
				'label' => 'Родительская страница',
			))
			->add('depth', 'text', array(
				'label' => 'Глубина',
			))
			->add('skip', 'text', array(
                'label'    => 'Пропустить',
                'required' => false,
			))
            ->add('isSubmenu', 'checkbox', array(
                'label'    => 'Субменю',
                'required' => false,
            ))
		;
    }

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NS\CmsBundle\Block\Settings\MenuBlockSettingsModel'
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_cmsbundle_menublocksettingsform';
    }
}
