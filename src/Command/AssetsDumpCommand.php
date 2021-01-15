<?php declare(strict_types=1);

namespace Torr\Assets\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Assets\Namespaces\NamespaceRegistry;
use Torr\Assets\Storage\AssetDumper;
use Torr\Assets\Storage\AssetMapCache;
use Torr\Rad\Command\TorrCliStyle;

final class AssetsDumpCommand extends Command
{
	protected static $defaultName = "21torr:assets:dump";
	private NamespaceRegistry $namespaceRegistry;
	private AssetDumper $assetDumper;
	private AssetMapCache $assetMapCache;

	/**
	 */
	public function __construct (
		NamespaceRegistry $namespaceRegistry,
		AssetDumper $assetDumper,
		AssetMapCache $assetMapCache
	)
	{
		parent::__construct();
		$this->namespaceRegistry = $namespaceRegistry;
		$this->assetDumper = $assetDumper;
		$this->assetMapCache = $assetMapCache;
	}

	/**
	 * @inheritDoc
	 */
	protected function execute (InputInterface $input, OutputInterface $output) : int
	{
		$io = new TorrCliStyle($input, $output);
		$io->title("Assets: Dump all assets");

		$io->section("Clear assetmap cache");
		$this->assetMapCache->clearCache();
		$io->writeln("<fg=green>Done</>");

		$io->section("Clear dump directory");
		$this->assetDumper->clearDumpDirectory();
		$io->writeln("<fg=green>Done</>");

		$io->newLine();
		$io->section("Dump assets");
		$result = $this->assetDumper->dumpNamespaces($this->namespaceRegistry->getNamespaces());
		$this->assetMapCache->setMapCache($result);

		$io->newLine(2);
		$io->success("All done.");

		return 0;
	}
}
