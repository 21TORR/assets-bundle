<?php declare(strict_types=1);

namespace Torr\Assets\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Assets\Dependency\DependencyMapLoader;
use Torr\Assets\Manager\AssetsManager;
use Torr\Rad\Command\TorrCliStyle;

final class AssetsDumpCommand extends Command
{
	protected static $defaultName = "21torr:assets:dump";
	private AssetsManager $assetsManager;
	private DependencyMapLoader $dependencyMapLoader;

	/**
	 */
	public function __construct (AssetsManager $assetsManager, DependencyMapLoader $dependencyMapLoader)
	{
		parent::__construct();
		$this->assetsManager = $assetsManager;
		$this->dependencyMapLoader = $dependencyMapLoader;
	}

	/**
	 * @inheritDoc
	 */
	protected function execute (InputInterface $input, OutputInterface $output) : int
	{
		$io = new TorrCliStyle($input, $output);
		$io->title("Assets: Dump all assets");

		$io->section("Clear AssetMapCache, AssetDependencyCollectionCache and dump directory");
		$this->assetsManager->clearStorageCache();
		$io->writeln("<fg=green>Done</>");
		$io->newLine();

		$io->section("Dump assets");
		$this->assetsManager->reimport($io);
		$io->writeln("<fg=green>Done</>");
		$io->newLine();

		$io->section("Refresh dependencies");
		$this->dependencyMapLoader->refresh();
		$io->writeln("<fg=green>Done</>");
		$io->newLine();

		$io->success("All done.");

		return 0;
	}
}
