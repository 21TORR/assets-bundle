<?php declare(strict_types=1);

namespace Torr\Assets\Storage;

use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Torr\Assets\Asset\Asset;
use Torr\Assets\File\FileLoader;
use Torr\Assets\File\FileTypeRegistry;
use Torr\Assets\Manager\AssetsManager;
use Torr\Assets\Namespaces\NamespaceRegistry;

final class AssetDumper implements ServiceSubscriberInterface
{
	private const SKIP_DEFERRED = true;
	private const ALL_ASSETS = false;

	private ContainerInterface $locator;
	private AssetStorage $storage;
	private NamespaceRegistry $namespaceRegistry;
	private FileLoader $fileLoader;
	private FileTypeRegistry $fileTypeRegistry;

	/**
	 */
	public function __construct (
		ContainerInterface $locator,
		AssetStorage $storage,
		NamespaceRegistry $namespaceRegistry,
		FileLoader $fileLoader,
		FileTypeRegistry $fileTypeRegistry
	)
	{
		$this->locator = $locator;
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
	public function dumpNamespaces (array $namespaces) : AssetMap
	{
		$assetsManager = $this->locator->get(AssetsManager::class);

		$assets = $this->findAssets($namespaces);
		$map = new AssetMap();

		// dump first pass (= everything without dependencies)
		$deferred = $this->dumpAssets($map, $assets, self::SKIP_DEFERRED);

		// save first draft in cache
		$assetsManager->setAssetMap($map);

		// then dump second pass (only the deferred ones)
		$this->dumpAssets($map, $deferred, self::ALL_ASSETS);

		// save final map in cache
		$assetsManager->setAssetMap($map);

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

			$content = $this->fileLoader->loadForProduction($assetMap, $asset);
			$storedAsset = $this->storage->storeAsset($asset, $content, $fileType->shouldHashFileName());

			$assetMap->add($storedAsset);
		}

		return $skipped;
	}

	/**
	 * @inheritDoc
	 */
	public static function getSubscribedServices ()
	{
		return [
			AssetsManager::class,
		];
	}


}
