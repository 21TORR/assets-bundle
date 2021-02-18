<?php declare(strict_types=1);

namespace Torr\Assets\Asset;

final class AssetDependency
{
	private StoredAsset $asset;
	private bool $modern;
	private bool $legacy;

	/**
	 */
	public function __construct (StoredAsset $asset)
	{
		$this->asset = $asset;
		$this->modern = false;
		$this->legacy = false;
	}

	/**
	 */
	public function getAsset () : StoredAsset
	{
		return $this->asset;
	}

	/**
	 */
	public function isModern () : bool
	{
		return $this->modern;
	}

	/**
	 */
	public function setModern (bool $modern) : void
	{
		$this->modern = $modern;
	}

	/**
	 */
	public function isLegacy () : bool
	{
		return $this->legacy;
	}

	/**
	 */
	public function setLegacy (bool $legacy) : void
	{
		$this->legacy = $legacy;
	}
}
