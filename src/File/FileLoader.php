<?php declare(strict_types=1);

namespace Torr\Assets\File;

use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\File\FileNotFoundException;
use Torr\Assets\File\Data\FileProcessData;
use Torr\Assets\Namespaces\NamespaceRegistry;
use Torr\Assets\Storage\AssetMap;

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
	public function loadFile (AssetMap $assetMap, Asset $asset, ?bool $mode)
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
			$processData = new FileProcessData($asset, $content, $filePath, $assetMap);

			return self::MODE_PRODUCTION === $mode
				? $fileType->processForProduction($processData)
				: $fileType->processForDebug($processData);
		}

		return $content;
	}
}
