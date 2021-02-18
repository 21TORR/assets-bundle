<?php declare(strict_types=1);

namespace Torr\Assets\File;

use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\File\FileNotFoundException;
use Torr\Assets\File\Data\FileProcessData;
use Torr\Assets\Namespaces\NamespaceRegistry;
use Torr\Assets\Storage\AssetMap;

final class FileLoader
{
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
	 * Loads the file for production usage
	 */
	public function loadForProduction (AssetMap $assetMap, Asset $asset) : string
	{
		[$filePath, $content] = $this->fetchFileContents($asset);
		$processData = new FileProcessData($asset, $content, $filePath, $assetMap);
		$fileType = $this->fileTypeRegistry->getFileType($asset);

		return $fileType->processForProduction($processData);
	}


	/**
	 * Loads the file for debug usage
	 */
	public function loadForDebug (AssetMap $assetMap, Asset $asset) : string
	{
		[$filePath, $content] = $this->fetchFileContents($asset);
		$processData = new FileProcessData($asset, $content, $filePath, $assetMap);
		$fileType = $this->fileTypeRegistry->getFileType($asset);

		return $fileType->processForDebug($processData);
	}


	/**
	 * Loads the unprocessed file contents
	 */
	public function loadUnprocessed (Asset $asset) : string
	{
		return $this->fetchFileContents($asset)[1];
	}


	/**
	 *
	 */
	private function fetchFileContents (Asset $asset) : array
	{
		$filePath = $this->namespaceRegistry->getAssetFilePath($asset);
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

		return [$filePath, $content];
	}

	public function loadFile () : void
	{

	}
}
