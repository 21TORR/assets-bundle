<?php
declare(strict_types=1);

namespace Torr\Assets\Asset;

/**
 * @final
 */
interface AssetInterface
{
	/**
	 * Returns the asset path of this asset
	 */
	public function toAssetPath () : string;

	/**
	 * Returns the local file path
	 */
	public function getPath () : string;

	/**
	 * Returns the namespace of this asset
	 */
	public function getNamespace () : string;
}
