<?php

namespace NS\CmsBundle\Block\Settings\Generic;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CountBlockSettingsForm
 * @package NS\CmsBundle\Block\Settings\Generic
 */
class CountBlockSettingsForm extends AbstractType
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
			->add('count', 'text', array(
				'label'    => 'Количество элементов на странице',
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
            'data_class' => 'NS\CmsBundle\Block\Settings\Generic\CountBlockSettingsModel'
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_cmsbundle__generic_countblocksettingsform';
    }
}
