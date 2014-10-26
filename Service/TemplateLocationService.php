<?php

namespace NS\CmsBundle\Service;

use Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

/**
 * Class TemplateLocationService
 *
 * @package NS\CmsBundle\Service
 */
class TemplateLocationService
{
    /**
     * @var TemplateNameParserInterface
     */
    private $templateNameParser;

    /**
     * @var TemplateLocator
     */
    private $templateLocator;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param TemplateNameParserInterface $templateNameParser
     * @param TemplateLocator             $templateLocator
     * @param KernelInterface             $kernel
     */
    public function __construct(TemplateNameParserInterface $templateNameParser,
                                TemplateLocator $templateLocator, KernelInterface $kernel)
    {
        $this->templateNameParser = $templateNameParser;
        $this->templateLocator    = $templateLocator;
        $this->kernel             = $kernel;
    }

    /**
     * Retrieves template file name (vendor or local if exists)
     *
     * @param string $template short template name
     * @return string
     */
    public function getTemplateFileName($template)
    {
        $reference = $this->templateNameParser->parse($template);
        return $this->templateLocator->locate($reference);
    }

    /**
     * Retrieves local template file name (app/Resources)
     *
     * @param string $template short template name
     * @return string
     */
    public function getLocalTemplateFileName($template)
    {
        $reference = $this->templateNameParser->parse($template);

        // transforming path
        $path = str_replace('@'.$reference->get('bundle').'/Resources/', '', $reference->getPath());

        return sprintf('%s/Resources/%s/%s',
            $this->kernel->getRootDir(),
            $reference->get('bundle'),
            $path
        );
    }

    /**
     * Retrieves vendor template file name
     *
     * @param string $template short template name
     * @return string|null
     */
    public function getVendorTemplateFileName($template)
    {
        $reference = $this->templateNameParser->parse($template);

        try {
            return $this->kernel->locateResource($reference->getPath());
        }
        catch (\InvalidArgumentException $e) {
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function getLocalTemplateNames()
    {
        $finder = new Finder();
        $finder->files()
            ->in($this->kernel->getRootDir() . '/Resources')
            ->ignoreDotFiles(true)
            ->name('*.html.twig')
            ->sortByName()
        ;

        $names = array();

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $fileName = str_replace($this->kernel->getRootDir() . '/Resources/', '', $file);
            $fileName = str_replace('/views/', ':', $fileName);
            $fileName = str_replace('/', ':', $fileName);
            $fileName = str_replace('views', ':', $fileName);
            $names[] = $fileName;
        }

        return $names;
    }
}