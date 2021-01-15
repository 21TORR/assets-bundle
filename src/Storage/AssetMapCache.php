<?php

namespace Torr\Assets\Storage;

use Psr\Cache\CacheItemPoolInterface;

final class AssetMapCache
{
	private const CACHE_KEY = '21torr.asset.asset_map';

	private CacheItemPoolInterface $cachePool;

	/**
	 * AssetMapCache constructor.
	 */
	public function __construct(CacheItemPoolInterface $cachePool)
	{
		$this->cachePool = $cachePool;
	}

	/**
	 * save AssetMap in Cache
	 */
	public function setMapCache(AssetMap $map) : void
	{
		$cacheItem = $this->cachePool->getItem(self::CACHE_KEY);
		$cacheItem->set($map);
		$this->cachePool->save($cacheItem);
	}

	/**
	 * get AssetMap from Cache
	 */
	public function getMapFromCache() : ?AssetMap
	{
		$cacheItem = $this->cachePool->getItem(self::CACHE_KEY);

		if($cacheItem->isHit()) {
			return $cacheItem->get();
		}

		return null;
	}

	/**
	 * clear AssetMap Cache
	 */
	public function clearCache() : void
	{
		$this->cachePool->deleteItem(self::CACHE_KEY);
	}
}