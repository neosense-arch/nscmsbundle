<?php
namespace NS\CmsBundle\Twig\Extension;

use NS\CmsBundle\Entity\Block;

/**
 * Class AreaExtension
 * @package NS\CmsBundle\Twig\Extension
 */
class AreaExtension extends \Twig_Extension
{
	/**
	 * @return array
	 */
	public function getFunctions()
	{
		return array(
			'ns_cms_area' => new \Twig_Function_Method($this, 'area', array('is_safe' => array('html'))),
		);
	}

	/**
	 * @param Block[] $blocks
	 * @param $areaName
	 * @return string
	 */
	public function area(array $blocks, $areaName)
	{
		$res = '';

		foreach ($blocks as $block) {
			if ($block->getAreaName() === $areaName) {
				$res .= $block->getHtml();
			}
		}

		return $res;
	}

	/**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
		return 'ns_cms_area';
    }
}