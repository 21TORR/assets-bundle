<?php

namespace Torr\Assets\Storage;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Torr\Assets\Command\AssetsDumpCommand;

final class AssetMapCache
{
	private const CACHE_KEY = '21torr.asset.asset_map';

	private CacheItemPoolInterface $cachePool;

	private KernelInterface $kernel;

	private AssetDumper $assetDumper;

	/**
	 * AssetMapCache constructor.
	 */
	public function __construct(CacheItemPoolInterface $cachePool, KernelInterface $kernel, AssetDumper $assetDumper,)
	{
		$this->cachePool = $cachePool;
		$this->kernel = $kernel;
		$this->assetDumper = $assetDumper;
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
	public function getMapCache() : ?AssetMap
	{
		$cacheItem = $this->cachePool->getItem(self::CACHE_KEY);

		if($cacheItem->isHit()) {
			return $cacheItem->get();
		}

		return null;
	}

	/**
	 * get AssetMap
	 * in debug mode return new empty AssetMap and clear dump directory
	 */
	public function getAssetMap() : ?AssetMap
	{
		if($this->kernel->isDebug() === false) {
			$this->assetDumper->clearDumpDirectory();
			return new AssetMap();
		}

		$assetMap = $this->getMapCache();

		// If no cache exist, execute the DumpCommand
		if(null === $assetMap) {
			$this->executeDumpCommand();
			$assetMap = $this->getMapCache();
		}

		return $assetMap;
	}

	/**
	 * clear AssetMap Cache
	 */
	public function clearCache() : void
	{
		$this->cachePool->deleteItem(self::CACHE_KEY);
	}

	/**
	 * execute AssetsDumpCommand
	 */
	private function executeDumpCommand() : void
	{
		$application = new Application($this->kernel);
		$application->setAutoExit(false);

		$input = new ArrayInput([
			'command' => AssetsDumpCommand::getDefaultName()
		]);
		$output = new NullOutput();

		$application->run($input, $output);
	}
}