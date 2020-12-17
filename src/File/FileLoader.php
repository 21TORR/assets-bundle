<?php declare(strict_types=1);

namespace Torr\Assets\File;

use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\File\FileNotFoundException;
use Torr\Assets\Namespaces\NamespaceRegistry;

final class FileLoader
{
	public const MODE_PRODUCTION = true;
	public const MODE_DEBUG = false;
	public const MODE_UNTOUCHED = null;
	private NamespaceRegistry $namespaceRegistry;
	private FileTypeRegistry $fileTypeRegistry;

	/**
	 */
	public function __construct (NamespaceRegistry $namespaceRegistry, FileTypeRegistry $fileTypeRegistry)
	{
		$this->namespaceRegistry = $namespaceRegistry;
		$this->fileTypeRegistry = $fileTypeRegistry;
	}

	/**
	 * Loads the file content
	 */
	public function loadFile (Asset $asset, ?bool $mode)
	{
		$filePath = $this->namespaceRegistry->getAssetFilePath($asset);
		$fileType = $this->fileTypeRegistry->getFileType($asset);
		$content = @\file_get_contents($filePath);

		if (false === $content)
		{
			throw new FileNotFoundException(\sprintf(
				"Asset '%s' not %s at '%s'",
				$asset->toAssetPath(),
				\is_file($filePath) ? "found" : "readable",
				$filePath
			));
		}

		if (self::MODE_UNTOUCHED !== $mode)
		{
			return self::MODE_PRODUCTION === $mode
				? $fileType->processForProduction($asset, $content, $filePath)
				: $fileType->processForDebug($asset, $content, $filePath);
		}

		return $content;
	}
}
