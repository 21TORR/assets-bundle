<?php declare(strict_types=1);

namespace Torr\Assets\Manager;

use Psr\Cache\CacheItemPoolInterface;
use Torr\Assets\Namespaces\NamespaceRegistry;
use Torr\Assets\Storage\AssetDumper;
use Torr\Assets\Storage\AssetStorageMap;
use Torr\Rad\Command\TorrCliStyle;

final class AssetsManager
{
	private const STORAGE_MAP_CACHE_KEY = "21torr.assets.storage_map";
	private NamespaceRegistry $namespaceRegistry;
	private AssetDumper $assetDumper;
	private CacheItemPoolInterface $cachePool;
	private ?AssetStorageMap $storageMap = null;
	private bool $isDebug;

	/**
	 */
	public function __construct (
		NamespaceRegistry $namespaceRegistry,
		AssetDumper $assetDumper,
		CacheItemPoolInterface $cachePool,
		bool $isDebug
	)
	{
		$this->namespaceRegistry = $namespaceRegistry;
		$this->assetDumper = $assetDumper;
		$this->cachePool = $cachePool;
		$this->isDebug = $isDebug;
	}


	/**
	 */
	public function clearAll () : void
	{
		// clear dump directory
		$this->assetDumper->clearDumpDirectory();

		// clear cache
		$this->cachePool->deleteItem(self::STORAGE_MAP_CACHE_KEY);
		$this->storageMap = null;
	}


	/**
	 */
	private function dumpAssets (?TorrCliStyle $io = null) : AssetStorageMap
	{
		$storageMap = $this->assetDumper->dumpNamespaces(
			$this->namespaceRegistry->getNamespaces(),
			$io
		);

		$cacheItem = $this->cachePool->getItem(self::STORAGE_MAP_CACHE_KEY);
		$cacheItem->set($this->storageMap);
		$this->cachePool->saveDeferred($cacheItem);

		return $storageMap;
	}


	/**
	 */
	public function reimport (?TorrCliStyle $io) : void
	{
		if (null !== $io)
		{
			$io->writeln("• Clearing the storage");
		}

		$this->clearAll();

		if (null !== $io)
		{
			$io->writeln("• Dumping the assets");
		}

		$this->dumpAssets($io);
	}


	/**
	 */
	public function getStorageMap () : AssetStorageMap
	{
		if (null === $this->storageMap)
		{
			if ($this->isDebug)
			{
				$this->storageMap = new AssetStorageMap();
			}
			else
			{
				$cacheItem = $this->cachePool->getItem(self::STORAGE_MAP_CACHE_KEY);
				$value = $cacheItem->get();

				$this->storageMap = $value instanceof AssetStorageMap
					? $value
					: $this->dumpAssets();
			}
		}

		return $this->storageMap;
	}
}
