<?php declare(strict_types=1);

namespace Torr\Assets\Storage;

use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Torr\Assets\Asset\Asset;
use Torr\Assets\File\FileLoader;
use Torr\Assets\File\FileTypeRegistry;
use Torr\Assets\Namespaces\NamespaceRegistry;
use Torr\Rad\Command\TorrCliStyle;

final class AssetDumper
{
	private const SKIP_DEFERRED = true;
	private const ALL_ASSETS = false;

	private AssetStorage $storage;
	private NamespaceRegistry $namespaceRegistry;
	private FileLoader $fileLoader;
	private FileTypeRegistry $fileTypeRegistry;

	/**
	 */
	public function __construct (
		AssetStorage $storage,
		NamespaceRegistry $namespaceRegistry,
		FileLoader $fileLoader,
		FileTypeRegistry $fileTypeRegistry
	)
	{
		$this->storage = $storage;
		$this->namespaceRegistry = $namespaceRegistry;
		$this->fileLoader = $fileLoader;
		$this->fileTypeRegistry = $fileTypeRegistry;
	}


	/**
	 * Clears (and removes) the dump directory
	 */
	public function clearDumpDirectory () : void
	{
		$this->storage->clearStorage();
	}


	/**
	 * Dumps all namespaces
	 */
	public function dumpNamespaces (array $namespaces, ?TorrCliStyle $io = null) : AssetStorageMap
	{
		if (null !== $io)
		{
			$io->writeln("• Finding all assets");
		}

		$assets = $this->findAssets($namespaces);
		$map = new AssetStorageMap();

		// dump first pass (= everything without dependencies)
		if (null !== $io)
		{
			$io->writeln("• Dumping non-deferred assets");
		}

		$deferred = $this->dumpAssets($io, $map, $assets, self::SKIP_DEFERRED);

		// then dump second pass (only the deferred ones)
		if (null !== $io)
		{
			$io->writeln("• Dumping deferred assets");
		}

		$this->dumpAssets($io, $map, $deferred, self::ALL_ASSETS);

		return $map;
	}


	/**
	 * Finds all assets to dump
	 *
	 * @return Asset[]
	 */
	private function findAssets (array $namespaces) : array
	{
		$assets = [];

		foreach ($namespaces as $namespace)
		{
			$path = $this->namespaceRegistry->getNamespacePath($namespace);

			try
			{
				$finder = Finder::create()
					->in($path)
					->files()
					->ignoreDotFiles(true)
					->ignoreUnreadableDirs();


				foreach ($finder as $file)
				{
					$asset = new Asset($namespace, $file->getRelativePathname());
					$assets[] = $asset;
				}
			}
			catch (DirectoryNotFoundException $exception)
			{
				// ignore missing base directories
			}
		}

		return $assets;
	}


	/**
	 * Dumps the list of given assets
	 *
	 * @param Asset[] $assets
	 * @param bool    $skipDeferred whether deferred assets should be skipped.
	 *
	 * @return Asset[] all skipped assets
	 */
	private function dumpAssets (
		?TorrCliStyle $io,
		AssetStorageMap $assetMap,
		array $assets,
		bool $skipDeferred
	) : array
	{
		$skipped = [];
		$progress = null;

		if (null !== $io)
		{
			$progress = $io->createProgressBar(\count($assets));
			$progress->setFormat(" %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% %message%");
		}

		foreach ($assets as $asset)
		{
			if (null !== $progress)
			{
				$progress->setMessage($asset->toAssetPath());
				$progress->advance();
			}

			$fileType = $this->fileTypeRegistry->getFileType($asset);

			// if we should skip the deferred assets, just collect them and skip
			if ($skipDeferred && $fileType->canHaveAssetDependencies())
			{
				$skipped[] = $asset;
				continue;
			}

			$content = $this->fileLoader->loadForProduction($assetMap, $asset);
			$storedAsset = $this->storage->storeAsset($asset, $content, $fileType->shouldHashFileName());

			$assetMap->add($storedAsset);
		}

		if (null !== $progress)
		{
			\assert($io !== null);
			$progress->clear();
			$io->newLine();
		}

		return $skipped;
	}
}
