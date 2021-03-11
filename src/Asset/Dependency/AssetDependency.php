<?php
declare(strict_types=1);

namespace Torr\Assets\Asset\Dependency;

use Torr\Assets\Asset\Asset;

final class AssetDependency
{
	private Asset $asset;
	private array $attributes;

	/**
	 */
	public function __construct(Asset $asset, array $attributes = [])
	{
		$this->asset = $asset;
		$this->attributes = $attributes;
	}

	/**
	 */
	public function getAsset() : Asset
	{
		return $this->asset;
	}

	/**
	 */
	public function getAttributes() : array
	{
		return $this->attributes;
	}
}
