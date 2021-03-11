<?php declare(strict_types=1);

namespace Torr\Assets\File\Data;

use Torr\Assets\Asset\Asset;
use Torr\Assets\Storage\AssetStorageMap;

final class FileProcessData
{
	private Asset $asset;
	private string $content;
	private string $filePath;
	private AssetStorageMap $assetMap;

	/**
	 */
	public function __construct (Asset $asset, string $content, string $filePath, AssetStorageMap $assetMap)
	{
		$this->asset = $asset;
		$this->content = $content;
		$this->filePath = $filePath;
		$this->assetMap = $assetMap;
	}

	/**
	 */
	public function getAsset () : Asset
	{
		return $this->asset;
	}

	/**
	 */
	public function getContent () : string
	{
		return $this->content;
	}

	/**
	 */
	public function getFilePath () : string
	{
		return $this->filePath;
	}

	/**
	 */
	public function getStorageMap () : AssetStorageMap
	{
		return $this->assetMap;
	}
}
