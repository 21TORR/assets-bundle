<?php declare(strict_types=1);

namespace Torr\Assets\Manager;

use Torr\Assets\Namespaces\NamespaceRegistry;
use Torr\Assets\Storage\AssetDependencyCollection;
use Torr\Assets\Storage\AssetDumper;
use Torr\Assets\Storage\AssetMap;

final class AssetsManager
{
	private NamespaceRegistry $namespaceRegistry;
	private AssetDumper $assetDumper;
	private CacheManager $cacheManager;

	/**
	 */
	public function __construct (
		NamespaceRegistry $namespaceRegistry,
		AssetDumper $assetDumper,
		CacheManager $cacheManager,
	)
	{
		$this->namespaceRegistry = $namespaceRegistry;
		$this->assetDumper = $assetDumper;
		$this->cacheManager = $cacheManager;
	}


	/**
	 */
	public function clearAll () : void
	{
		$this->cacheManager->clearCache(CacheManager::ASSET_MAP_CACHE_KEY);
		$this->cacheManager->clearCache(CacheManager::ASSET_DEP_COLLECTION_CACHE_KEY);
		$this->assetDumper->clearDumpDirectory();
	}

	/**
	 */
	public function dumpAssets () : void
	{
		$this->assetDumper->dumpNamespaces($this->namespaceRegistry->getNamespaces());
	}

	/**
	 */
	public function dump () : void
	{
		$this->clearAll();
		$this->dumpAssets();
	}

	/**
	 */
	public function setAssetMap (AssetMap $data) : void
	{
		$this->cacheManager->setCache($data, CacheManager::ASSET_MAP_CACHE_KEY);
	}

	/**
	 * @param AssetDependencyCollection[] $data
	 */
	public function setDependencyCollection (array $data) : void
	{
		$this->cacheManager->setCache($data, CacheManager::ASSET_DEP_COLLECTION_CACHE_KEY);
	}

	/**
	 */
	public function getAssetMap () : AssetMap
	{
		$assetMap = $this->cacheManager->getCache(CacheManager::ASSET_MAP_CACHE_KEY);

		// If no cache exist, execute the DumpCommand
		if (null === $assetMap)
		{
			$this->dump();
			$assetMap = $this->cacheManager->getCache(CacheManager::ASSET_MAP_CACHE_KEY);
		}

		return $assetMap;
	}

	/**
	 * @return AssetDependencyCollection[]|null
	 */
	public function getAllCollections () : ?array
	{
		$collection = $this->cacheManager->getCache(CacheManager::ASSET_DEP_COLLECTION_CACHE_KEY);

		// If no cache exist, execute the DumpCommand
		if (null === $collection)
		{
			// TODO Add fallback if no collection was found in cache
			$this->dump();
			$collection = $this->cacheManager->getCache(CacheManager::ASSET_DEP_COLLECTION_CACHE_KEY);
		}

		return $collection;
	}

	/**
	 */
	public function getCollectionByPath (string $path) : ?AssetDependencyCollection
	{
		return $this->getAllCollections()[$path] ?? null;
	}
}
