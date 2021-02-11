<?php declare(strict_types=1);

namespace Torr\Assets\Asset;

final class StoredAsset
{
	private Asset $asset;
	/**
	 * base64 encoded hash of the file content
	 */
	private string $hash;
	private string $hashAlgorithm;
	private string $storedFilePath;

	/**
	 */
	public function __construct (
		Asset $asset,
		string $hash,
		string $hashAlgorithm,
		string $storedFilePath
	)
	{
		$this->asset = $asset;
		$this->hash = $hash;
		$this->hashAlgorithm = $hashAlgorithm;
		$this->storedFilePath = $storedFilePath;
	}

	/**
	 */
	public function toAssetPath () : string
	{
		return $this->asset->toAssetPath();
	}

	/**
	 */
	public function getStoredFilePath () : string
	{
		return $this->storedFilePath;
	}

	/**
	 */
	public function getAsset () : Asset
	{
		return $this->asset;
	}
}
