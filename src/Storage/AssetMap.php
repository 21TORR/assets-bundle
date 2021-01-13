<?php declare(strict_types=1);

namespace Torr\Assets\Storage;

use Torr\Assets\Asset\StoredAsset;

final class AssetMap
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
}
