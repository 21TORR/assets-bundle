<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\Exception\File\Type\NoEmbedSupport;
use Torr\Assets\File\Data\FileProcessData;
use Torr\HtmlBuilder\Node\HtmlElement;

abstract class FileType
{
	/**
	 * Returns the supported file extensions
	 *
	 * @return string[]
	 */
	abstract public function getSupportedExtensions () : array;


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
	public function createHtmlIncludeElement (string $url, array $attributes = []) : HtmlElement
	{
		throw new NoEmbedSupport();
	}
}
