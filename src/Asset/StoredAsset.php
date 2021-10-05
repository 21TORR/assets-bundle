<?php declare(strict_types=1);

namespace Torr\Assets\Asset;

final class StoredAsset implements AssetInterface
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
		string $storedFilePath,
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
	public function getPath() : string
	{
		return $this->storedFilePath;
	}


	/**
	 */
	public function getNamespace() : string
	{
		return $this->asset->getNamespace();
	}


	/**
	 */
	public function getAsset () : Asset
	{
		return $this->asset;
	}

	/**
	 */
	public function getHash() : string
	{
		return $this->hash;
	}

	/**
	 */
	public function getHashAlgorithm() : string
	{
		return $this->hashAlgorithm;
	}
}
