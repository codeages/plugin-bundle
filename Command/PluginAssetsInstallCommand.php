<?php

namespace Codeages\PluginBundle\Command;

use Codeages\PluginBundle\System\PluginConfigurationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PluginAssetsInstallCommand extends Command
{
    private $container;

    public function __construct($container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setName('plugin:assets-install')
            ->addArgument('code', InputArgument::OPTIONAL, 'Plugin code (omit to install all installed plugins)')
            ->addOption('copy', null, InputOption::VALUE_NONE, 'Copy instead of symlink (default: symlink with relative path)')
            ->addOption('absolute', null, InputOption::VALUE_NONE, 'Use absolute path for symlink (default: relative)')
            ->addOption('no-cleanup', null, InputOption::VALUE_NONE, 'Do not remove static-dist dirs of uninstalled plugins')
            ->setDescription('Install plugin static-dist assets into web/static-dist (default: symlink --relative).');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootDir = dirname($this->container->getParameter('kernel.root_dir'));
        $pluginBaseDir = $rootDir.'/plugins';
        $targetBaseDir = $rootDir.'/web/static-dist';

        $codeArg = $input->getArgument('code');
        $copy = $input->getOption('copy');
        $absolute = $input->getOption('absolute');
        $noCleanup = $input->getOption('no-cleanup');

        $filesystem = new Filesystem();

        if ($codeArg !== null) {
            $codes = array(ucfirst($codeArg));
        } else {
            $manager = new PluginConfigurationManager($this->container->getParameter('kernel.root_dir'));
            $plugins = $manager->getInstalledPlugins();
            $codes = array();
            foreach ($plugins as $plugin) {
                if (!empty($plugin['type']) && $plugin['type'] !== 'plugin') {
                    continue;
                }
                $codes[] = ucfirst($plugin['code']);
            }
        }

        $validTargetDirs = array();
        foreach ($codes as $code) {
            $originDir = $pluginBaseDir.'/'.$code.'Plugin/Resources/static-dist/'.strtolower($code).'plugin';
            if (!is_dir($originDir)) {
                $output->writeln(sprintf('  <comment>Skip %s (no Resources/static-dist/%splugin)</comment>', $code, strtolower($code)));
                continue;
            }

            $targetDir = $targetBaseDir.'/'.strtolower($code).'plugin';
            $validTargetDirs[] = strtolower($code).'plugin';

            try {
                $filesystem->remove($targetDir);
                if ($copy) {
                    $filesystem->mirror($originDir, $targetDir, null, array('override' => true, 'delete' => true));
                    $output->writeln(sprintf('  <info>%s</info> -> %s (copy)', $code, $targetDir));
                } elseif ($absolute) {
                    $this->absoluteSymlink($filesystem, $originDir, $targetDir);
                    $output->writeln(sprintf('  <info>%s</info> -> %s (symlink absolute)', $code, $targetDir));
                } else {
                    $this->relativeSymlink($filesystem, $originDir, $targetDir);
                    $output->writeln(sprintf('  <info>%s</info> -> %s (symlink)', $code, $targetDir));
                }
            } catch (\Exception $e) {
                $output->writeln(sprintf('  <error>%s: %s</error>', $code, $e->getMessage()));

                return 1;
            }
        }

        if (!$noCleanup && is_dir($targetBaseDir)) {
            $finder = Finder::create()->depth(0)->directories()->in($targetBaseDir);
            foreach ($finder as $dir) {
                $name = $dir->getFilename();
                if (substr($name, -6) !== 'plugin' || in_array($name, $validTargetDirs)) {
                    continue;
                }
                $filesystem->remove($dir->getPathname());
                $output->writeln(sprintf('  Removed stale: %s', $name));
            }
        }

        $output->writeln('<info>Done.</info>');

        return 0;
    }

    private function relativeSymlink(Filesystem $filesystem, $originDir, $targetDir)
    {
        $filesystem->mkdir(dirname($targetDir));
        $relativeOrigin = $filesystem->makePathRelative(realpath($originDir), realpath(dirname($targetDir)));
        $filesystem->symlink($relativeOrigin, $targetDir, true);
    }

    private function absoluteSymlink(Filesystem $filesystem, $originDir, $targetDir)
    {
        $filesystem->mkdir(dirname($targetDir));
        $filesystem->symlink(realpath($originDir), $targetDir);
    }
}
