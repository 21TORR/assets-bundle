<?php declare(strict_types=1);

namespace Torr\Assets\File;

use Torr\Assets\Asset\Asset;
use Torr\Assets\File\Type\FileType;
use Torr\Assets\File\Type\GenericFileType;

final class FileTypeRegistry
{
	/** @var array<string, FileType> */
	private array $extensions = [];
	private FileType $genericFileType;

	/**
	 */
	public function __construct (iterable $fileTypes, ?FileType $genericFileType = null)
	{
		$this->genericFileType = $genericFileType ?? new GenericFileType();

		foreach ($fileTypes as $fileType)
		{
			$this->registerFileType($fileType);
		}
	}


	/**
	 * Registers a new file type.
	 */
	public function registerFileType (FileType $fileType) : void
	{
		foreach ($fileType->getSupportedExtensions() as $extension)
		{
			// always let the first one (with the highest priority) handle the type
			if (\array_key_exists($extension, $this->extensions))
			{
				continue;
			}

			$this->extensions[$extension] = $fileType;
		}
	}


	/**
	 */
	public function getFileType (Asset $asset) : FileType
	{
		return $this->extensions[$asset->getExtension()] ?? $this->genericFileType;
	}


	/**
	 * @return array<string, FileType>
	 */
	public function getExtensionMapping () : array
	{
		return $this->extensions;
	}
}
