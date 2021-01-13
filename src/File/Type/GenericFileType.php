<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

/**
 * @internal
 */
final class GenericFileType extends FileType
{
	/**
	 * @inheritDoc
	 */
	public function getSupportedExtensions () : array
	{
		// it is not defined for any extension, it should only be used as fallback generic file
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function shouldBeCompressed () : bool
	{
		return false;
	}
}
