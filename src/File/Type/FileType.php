<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\Asset\Asset;

abstract class FileType
{
	/**
	 * Returns the supported file extensions
	 *
	 * @return string[]
	 */
	abstract public function getSupportedExtensions () : array;


	/**
	 * Processes the file for usage in production
	 */
	public function processForProduction (Asset $asset, string $content, string $filePath) : string
	{
		return $content;
	}


	/**
	 * Processes the file for usage in debug
	 */
	public function processForDebug (Asset $asset, string $content, string $filePath) : string
	{
		return $content;
	}
}
