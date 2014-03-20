<?php

namespace NS\CmsBundle\Command;

use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Entity\PageRepository;
use NS\CmsBundle\Service\PageService;
use NS\CoreBundle\Service\VersionService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\ArrayInput;
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
			->setDescription('Installs CMS')
		;
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 * @return int|null|void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
        // greetings
        $this->stepGreetings($input, $output);
        $output->writeln("");

        // creating database
        $this->stepCreateDatabase($input, $output);
        $output->writeln("");

        // updating schema
        $this->stepUpdateSchema($input, $output);
        $output->writeln("");

        // installing assets
        $this->stepAssetsInstall($input, $output);
        $output->writeln("");

        // dumping assetic
        $this->stepAsseticDump($input, $output);
        $output->writeln("");

        // creating user
        $this->stepCreateAdmin($input, $output);
        $output->writeln("");

        // creating user bundle
        $this->stepCreateBundle($input, $output);
        $output->writeln("");

        // installing cms bundle
        $this->stepInstallCmsBundle($input, $output);
        $output->writeln("");

        // installing catalog
        $this->stepInstallCatalog($input, $output);
        $output->writeln("");

        $output->writeln("<info>DONE!</info>");
	}

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    private function stepGreetings(InputInterface $input, OutputInterface $output)
    {
        // retrieving engine version
        /** @var VersionService $versionService */
        $versionService = $this->getContainer()->get('ns_core.service.version');
        $version = $versionService->getVersion();

        // greetings
        $output->writeln("<info>NS Engine Installer</info>");
        $output->writeln("Engine version: <info>{$version}</info>");

        // confirming install
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        if (!$dialog->askConfirmation($output, "This will start install process. Continue? [Y/n] ")) {
            throw new \Exception('Canceled');
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function stepCreateDatabase(InputInterface $input, OutputInterface $output)
    {
        $database = $this->getContainer()->getParameter('database_name');

        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        if ($dialog->askConfirmation($output, "Do you want to create database <info>{$database}</info>? [Y/n] ")) {
            $this->runCommand('doctrine:database:create', array(), $output);
        }
        else {
            $output->writeln("Skipping database creation");
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function stepUpdateSchema(InputInterface $input, OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        if ($dialog->askConfirmation($output, "Do you want to <info>update db schema</info>? [Y/n] ")) {
            $this->runCommand('doctrine:schema:update', array('--force' => true), $output);
        }
        else {
            $output->writeln("Skipping schema update");
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function stepAssetsInstall(InputInterface $input, OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        if ($dialog->askConfirmation($output, "Do you want to <info>install assets</info>? [Y/n] ")) {
            $this->runCommand('assets:install', array(), $output);
        }
        else {
            $output->writeln("Skipping assets installation");
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function stepAsseticDump(InputInterface $input, OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        if ($dialog->askConfirmation($output, "Do you want to <info>dump assetic</info>? [Y/n] ")) {
            $this->runCommand('assetic:dump', array(), $output);
        }
        else {
            $output->writeln("Skipping assetic dumping");
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function stepCreateBundle(InputInterface $input, OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        if ($dialog->askConfirmation($output, "Do you want to <info>create user bundle</info>? [Y/n] ")) {
            $output->writeln("<error>ATTENTION</error> Please use this format: <info>DM/FrontBundle (DMFrontBundle) for site domain.com</info>");
            $this->runCommand('generate:bundle', array(), $output);
        }
        else {
            $output->writeln("Skipping user bundle generating");
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function stepCreateAdmin(InputInterface $input, OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        if ($dialog->askConfirmation($output, "Do you want to <info>create admin</info>? [Y/n] ")) {
            $email = $dialog->ask($output, "Email: ");
            $password = $dialog->ask($output, "Password: ");
            $this->runCommand('fos:user:create', array(
                'username' => $email,
                'email'    => $email,
                'password' => $password,
            ), $output);

            $this->runCommand('fos:user:promote', array(
                'username' => $email,
                'role'     => 'ROLE_ADMIN',
            ), $output);
        }
        else {
            $output->writeln("Skipping admin creation");
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function stepInstallCmsBundle(InputInterface $input, OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        if ($dialog->askConfirmation($output, "Do you want to <info>install main CMS bundle</info>? [Y/n] ")) {
            /** @var PageService $pageService */
            $pageService = $this->getContainer()->get('ns_cms.service.page');

            $output->writeln("Installing <info>Cms bundle</info>");

            // creating main page
            $output->writeln("Creating main page");
            $mainPage = $pageService->getMainPageOrCreate();
            $output->writeln("Main page installed (#{$mainPage->getId()})\n");
        }
        else {
            $output->writeln("Skipping CMS bundle installation");
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function stepInstallCatalog(InputInterface $input, OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        if ($dialog->askConfirmation($output, "Do you want to <info>install catalog</info>? [Y/n] ")) {
            $this->runCommand('ns:catalog:install', array(), $output);
        }
        else {
            $output->writeln("Skipping catalog installation");
        }
    }

    /**
     * @param                 $name
     * @param array           $input
     * @param OutputInterface $output
     * @return int
     */
    private function runCommand($name, array $input, OutputInterface $output)
    {
        $input['command'] = $name;
        $command = $this->getApplication()->find($name);
        return $command->run(new ArrayInput($input), $output);
    }
}