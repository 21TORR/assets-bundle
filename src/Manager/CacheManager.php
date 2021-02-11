<?php

namespace Torr\Assets\Manager;

use Psr\Cache\CacheItemPoolInterface;

final class CacheManager
{
	private const APP_CACHE_KEY = '21torr.assets.';

	public const ASSET_MAP_CACHE_KEY = self::APP_CACHE_KEY . 'asset_map';
	public const ASSET_DEP_COLLECTION_CACHE_KEY = self::APP_CACHE_KEY . 'asset_dependency_collection';

	protected CacheItemPoolInterface $cachePool;

	/**
	 * AbstractCache constructor.
	 */
	public function __construct (CacheItemPoolInterface $cachePool)
	{
		$this->cachePool = $cachePool;
	}

	/**
	 * save AssetMap in Cache
	 */
	public function setCache ($data, string $cacheKey) : void
	{
		$cacheItem = $this->cachePool->getItem($cacheKey);
		$cacheItem->set($data);
		$this->cachePool->save($cacheItem);
	}

	/**
	 * get from Cache
	 */
	public function getCache (string $cacheKey)
	{
		$cacheItem = $this->cachePool->getItem($cacheKey);

		return $cacheItem->isHit()
			? $cacheItem->get()
			: null;
	}

	/**
	 * clear AssetMap Cache
	 */
	public function clearCache (string $cacheKey) : void
	{
		$this->cachePool->deleteItem($cacheKey);
	}
}
