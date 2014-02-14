<?php

namespace NS\CmsBundle;

use NS\CoreBundle\Bundle\CoreBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NSCmsBundle extends Bundle implements CoreBundle
{
    /**
     * Retrieves human-readable bundle title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Системные';
    }
}
