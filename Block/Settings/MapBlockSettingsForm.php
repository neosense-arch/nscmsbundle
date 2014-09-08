<?php

namespace NS\CmsBundle\Block\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Map block settings form
 *
 */
class MapBlockSettingsForm extends AbstractType
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
			->add('apiKey', 'text', array(
                'label'    => 'API Key',
                'required' => false,
			))
			->add('lat', 'text', array(
				'label' => 'Широта (lat)',
			))
			->add('lng', 'text', array(
				'label' => 'Долгота (lng)',
			))
			->add('markerLat', 'text', array(
				'label' => 'Широта маркера (lat)',
                'required' => false,
			))
			->add('markerLng', 'text', array(
				'label' => 'Долгота маркера (lng)',
                'required' => false,
			))
			->add('markerTitle', 'text', array(
				'label' => 'Надпись на маркере',
                'required' => false,
			))
			->add('zoom', 'text', array(
				'label' => 'Зум',
                'required' => false,
			))
			->add('width', 'text', array(
				'label' => 'Ширина',
                'required' => false,
			))
			->add('height', 'text', array(
				'label' => 'Высота',
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
            'data_class' => 'NS\CmsBundle\Block\Settings\MapBlockSettingsModel'
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_cms_block_map';
    }
}
