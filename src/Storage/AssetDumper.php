<?php declare(strict_types=1);

namespace Torr\Assets\Storage;

use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Torr\Assets\Asset\Asset;
use Torr\Assets\File\FileLoader;
use Torr\Assets\File\FileTypeRegistry;
use Torr\Assets\Manager\AssetsManager;
use Torr\Assets\Namespaces\NamespaceRegistry;

final class AssetDumper
{
	private const SKIP_DEFERRED = true;
	private const ALL_ASSETS = false;

	private AssetStorage $storage;
	private NamespaceRegistry $namespaceRegistry;
	private FileLoader $fileLoader;
	private FileTypeRegistry $fileTypeRegistry;
	private AssetsManager $assetsManager;

	/**
	 */
	public function __construct (
		AssetStorage $storage,
		NamespaceRegistry $namespaceRegistry,
		FileLoader $fileLoader,
		FileTypeRegistry $fileTypeRegistry,
		AssetsManager $assetsManager
	)
	{
		$this->storage = $storage;
		$this->namespaceRegistry = $namespaceRegistry;
		$this->fileLoader = $fileLoader;
		$this->fileTypeRegistry = $fileTypeRegistry;
		$this->assetsManager = $assetsManager;
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
	public function dumpNamespaces (array $namespaces) : AssetMap
	{
		$assets = $this->findAssets($namespaces);
		$map = new AssetMap();

		// dump first pass (= everything without dependencies)
		$deferred = $this->dumpAssets($map, $assets, self::SKIP_DEFERRED);

		// save first draft in cache
		$this->assetsManager->setAssetMap($map);

		// then dump second pass (only the deferred ones)
		$this->dumpAssets($map, $deferred, self::ALL_ASSETS);

		// save final map in cache
		$this->assetsManager->setAssetMap($map);

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
	 * @param bool $skipDeferred whether deferred assets should be skipped.
	 *
	 * @return Asset[] all skipped assets
	 */
	private function dumpAssets (AssetMap $assetMap, array $assets, bool $skipDeferred) : array
	{
		$skipped = [];

		foreach ($assets as $asset)
		{
			$fileType = $this->fileTypeRegistry->getFileType($asset);

			// if we should skip the deferred assets, just collect them and skip
			if ($skipDeferred && $fileType->canHaveAssetDependencies())
			{
				$skipped[] = $asset;
				continue;
			}

			$content = $this->fileLoader->loadFile($assetMap, $asset, FileLoader::MODE_PRODUCTION);
			$storedAsset = $this->storage->storeAsset($asset, $content, $fileType->shouldHashFileName());

			$assetMap->add($storedAsset);
		}

		return $skipped;
	}
}
