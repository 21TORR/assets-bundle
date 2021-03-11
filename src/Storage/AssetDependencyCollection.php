<?php declare(strict_types=1);

namespace Torr\Assets\Storage;

use Torr\Assets\Asset\Asset;
use Torr\Assets\Asset\Dependency\AssetDependency;
use Torr\Assets\Exception\Asset\InvalidAssetException;

final class AssetDependencyCollection
{
	/** @var AssetDependency[] */
	private array $collection;
	private string $namespace;
	private string $path;

	/**
	 */
	public function __construct (string $namespace, string $path)
	{
		$this->collection = [];

		if (!\preg_match('~^' . Asset::NAMESPACE_REGEX . '$~', $namespace))
		{
			throw new InvalidAssetException(\sprintf("Invalid asset namespace: '%s'", $namespace));
		}

		if (!\preg_match('~^' . Asset::PATH_REGEX . '$~', $path))
		{
			throw new InvalidAssetException(\sprintf("Invalid asset path: '%s'", $path));
		}

		$this->namespace = $namespace;
		$this->path = $path;
	}

	/**
	 * @return AssetDependency[]
	 */
	public function getCollection () : array
	{
		return $this->collection;
	}

	/**
	 */
	public function addToCollection (AssetDependency $assetDependency) : void
	{
		$this->collection[] = $assetDependency;
	}

	/**
	 */
	public function getNamespace () : string
	{
		return $this->namespace;
	}

	/**
	 */
	public function getPath () : string
	{
		return $this->path;
	}

	/**
	 * Transforms the asset back to an asset path
	 */
	public function toAssetPath () : string
	{
		return "@{$this->namespace}/{$this->path}";
	}
}
