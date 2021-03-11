<?php declare(strict_types=1);

namespace Torr\Assets\Manager;

use Psr\Cache\CacheItemPoolInterface;
use Torr\Assets\Storage\AssetStorageMap;

final class CacheManager
{
	private const APP_CACHE_KEY = '21torr.assets.';

	public const ASSET_MAP_CACHE_KEY = self::APP_CACHE_KEY . 'asset_map';
	public const ASSET_DEP_COLLECTION_CACHE_KEY = self::APP_CACHE_KEY . 'asset_dependency_collection';

	private CacheItemPoolInterface $cachePool;

	/**
	 * AbstractCache constructor.
	 */
	public function __construct (CacheItemPoolInterface $cachePool)
	{
		$this->cachePool = $cachePool;
	}

	/**
	 * Save AssetMap in Cache
	 *
	 * @param mixed $data
	 */
	public function setCache ($data, string $cacheKey) : void
	{
		$cacheItem = $this->cachePool->getItem($cacheKey);
		$cacheItem->set($data);
		$this->cachePool->save($cacheItem);
	}

	/**
	 * Get AssetMap from Cache
	 *
	 * @return mixed
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
