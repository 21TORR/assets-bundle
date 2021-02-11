<?php declare(strict_types=1);

namespace Torr\Assets\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Assets\Manager\AssetsManager;
use Torr\Rad\Command\TorrCliStyle;

final class AssetsDumpCommand extends Command
{
	protected static $defaultName = "21torr:assets:dump";
	private AssetsManager $assetsManager;

	/**
	 */
	public function __construct (AssetsManager $assetsManager)
	{
		parent::__construct();
		$this->assetsManager = $assetsManager;
	}

	/**
	 * @inheritDoc
	 */
	protected function execute (InputInterface $input, OutputInterface $output) : int
	{
		$io = new TorrCliStyle($input, $output);
		$io->title("Assets: Dump all assets");

		$io->section("Clear AssetMapCache, AssetDependencyCollectionCache and dump directory");
		$this->assetsManager->clearAll();
		$io->writeln("<fg=green>Done</>");
		$io->newLine();

		$io->section("Dump assets");
		$this->assetsManager->dumpAssets();
		$io->writeln("<fg=green>Done</>");
		$io->newLine();

		$io->section("Register dependency");
		$this->assetsManager->registerDependency();
		$io->writeln("<fg=green>Done</>");
		$io->newLine(2);

		$io->success("All done.");

		return 0;
	}
}
