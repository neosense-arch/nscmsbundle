<?php

namespace NS\CmsBundle\Exception;

use NS\CmsBundle\Entity\Block;

/**
 * Class BlockException
 *
 * @package NS\CmsBundle\Exception
 */
class BlockException extends \Exception
{
	/**
	 * Constructor
	 *
	 * @param string $message
	 * @param Block  $block
	 */
	public function __construct($message = '', Block $block = null)
	{
		if ($block) {
			$message = sprintf(
				'%s (block #%s of type "%s")',
				$message,
				$block->getId(),
				$block->getTypeName()
			);
		}

		parent::__construct($message);
	}
} 