<?php

namespace NS\CmsBundle\Entity;

use RecursiveIterator;

/**
 * Class PageRecursiveIterator
 *
 * @package NS\CmsBundle\Entity
 */
class PageRecursiveIterator extends \ArrayIterator implements \RecursiveIterator
{
	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Returns if an iterator can be created for the current entry.
	 *
	 * @link http://php.net/manual/en/recursiveiterator.haschildren.php
	 * @return bool true if the current entry can be iterated over, otherwise returns false.
	 */
	public function hasChildren()
	{
		return $this->current()->hasChildren();
	}
	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Returns an iterator for the current entry.
	 *
	 * @link http://php.net/manual/en/recursiveiterator.getchildren.php
	 * @return RecursiveIterator An iterator for the current entry.
	 */
	public function getChildren()
	{
		return new self($this->current()->getChildren()->toArray());
	}
}