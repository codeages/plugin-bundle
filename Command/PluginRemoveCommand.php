<?php

namespace Codeages\PluginBundle\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Codeages\PluginBundle\System\PluginRegister;

class PluginRemoveCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    public function getContainer()
    {
        return $this->container;
    }

    protected function configure()
    {
        $this
            ->setName('plugin:remove')
            ->addArgument('code', InputArgument::REQUIRED, 'Plugin code.')
            ->addOption('with-deleting-database', null, InputOption::VALUE_NONE, 'remove database?')
            ->setDescription('Remove plugin.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $biz = $this->getContainer()->get('biz');
        $rootDir = dirname($this->getContainer()->getParameter('kernel.root_dir'));

        $code = $input->getArgument('code');

        $register = new PluginRegister($rootDir, 'plugins', $biz);

        $output->writeln(sprintf('Remove plugin <comment>%s</comment> :', $code));

        $output->write('  - Remove plugin registed info.');
        $metas = $register->removePlugin($code);
        $output->writeln('  <info>[Ok]</info>');

        $output->write('  - Refresh plugin cache.');
        $register->refreshInstalledPluginConfiguration();
        $output->writeln('<info>[Ok]</info>');

        $output->writeln("<info>Finished!</info>\n");
    }
}
