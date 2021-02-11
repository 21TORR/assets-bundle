<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\Exception\File\Type\NoEmbedSupport;
use Torr\Assets\File\Data\FileProcessData;

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
	public function processForProduction (FileProcessData $data) : string
	{
		return $data->getContent();
	}


	/**
	 * Processes the file for usage in debug
	 */
	public function processForDebug (FileProcessData $data) : string
	{
		return $data->getContent();
	}

	/**
	 * Returns whether the file should be compressed
	 */
	public function shouldBeCompressed () : bool
	{
		return true;
	}

	/**
	 * Returns whether the file name of these files should be hashed
	 */
	public function shouldHashFileName () : bool
	{
		return true;
	}

	/**
	 * Return whether the files of this type can have dependencies to other assets
	 */
	public function canHaveAssetDependencies () : bool
	{
		return false;
	}

	/**
	 */
	public function getEmbedCode (string $path, array $parameter = []) : string
	{
		throw new NoEmbedSupport();
	}
}
