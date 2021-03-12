<?php declare(strict_types=1);

namespace Torr\Assets\Storage;

use Torr\Assets\Asset\Asset;
use Torr\Assets\Asset\AssetInterface;
use Torr\Assets\Asset\StoredAsset;

final class AssetStorageMap
{
	/**
	 * The map of asset path to stored asset
	 *
	 * @var array<string, StoredAsset>
	 */
	private array $map = [];


	/**
	 */
	public function add (StoredAsset $asset) : void
	{
		$this->map[$asset->toAssetPath()] = $asset;
	}

	/**
	 */
	public function get (Asset $asset) : AssetInterface
	{
		return $this->map[$asset->toAssetPath()] ?? $asset;
	}
}
