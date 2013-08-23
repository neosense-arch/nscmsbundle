<?php

namespace NS\CmsBundle\Command;

use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Entity\PageRepository;
use NS\CmsBundle\Service\PageService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallCommand
 *
 * @package NS\CmsBundle\Command
 */
class InstallCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('ns:cms:install')
			->setDescription('Installs CMS bundle')
		;
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 * @return int|null|void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("<info>Installing CMS bundle</info>\n");

		$pageService = $this->getPageService();

		$output->writeln("Installing main page...");
		$mainPage = $pageService->getMainPageOrCreate();
		$output->writeln("Main page installed (#{$mainPage->getId()})\n");

		$output->writeln("<info>Done</info>");
	}

	/**
	 * @return PageService
	 */
	private function getPageService()
	{
		return $this->getContainer()->get('ns_cms.service.page');
	}
} 