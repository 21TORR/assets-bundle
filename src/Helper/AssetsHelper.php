<?php declare(strict_types=1);

namespace Torr\Assets\Helper;

use Torr\Assets\Asset\Asset;

/**
 * Convenience wrapper around commonly used asset functions.
 * Your project should probably only ever use this class directly.
 *
 * @api
 */
final class AssetsHelper
{
	/**
	 * Returns the embed code for the given asset
	 *
	 * @param Asset|string $asset
	 */
	public function embed ($asset) : string
	{
		$value = Asset::create($asset);
		return "embed";
	}


	/**
	 * Returns the url to the given asset
	 *
	 * @param Asset|string $asset
	 */
	public function getUrl ($asset) : string
	{
		$value = Asset::create($asset);
		return "url";
	}
}
