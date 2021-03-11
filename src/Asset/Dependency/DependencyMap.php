<?php
declare(strict_types=1);

namespace Torr\Assets\Asset\Dependency;

use Torr\Assets\Asset\Asset;

final class DependencyMap
{
	private array $map = [];


	/**
	 * Registers a new dependency
	 */
	public function registerDependency (Asset $asset, AssetDependency $dependency) : void
	{
		$this->map[$asset->toAssetPath()][] = $dependency;
	}


	/**
	 * Gets all dependencies for the given asset
	 *
	 * @return AssetDependency[]
	 */
	public function getDependencies (Asset $asset) : array
	{
		return $this->map[$asset->toAssetPath()] ?? [new AssetDependency($asset)];
	}
}
