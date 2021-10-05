<?php declare(strict_types=1);

namespace Torr\Assets\Asset;

use Torr\Assets\Exception\Asset\InvalidAssetException;
use Torr\Rad\Exception\UnexpectedTypeException;

final class Asset implements AssetInterface
{
	public const NAMESPACE_REGEX = "^[a-zA-Z][a-zA-Z0-9_-]*?$";
	public const PATH_REGEX = "^([a-zA-Z0-9_\\-.]+/)*[a-zA-Z0-9_\\-.]+$";
	private string $namespace;
	private string $path;
	private ?string $extension = null;

	/**
	 */
	public function __construct (string $namespace, string $path)
	{
		if (!\preg_match('~^' . self::NAMESPACE_REGEX . '$~', $namespace))
		{
			throw new InvalidAssetException(\sprintf("Invalid asset namespace: '%s'", $namespace));
		}

		if (!\preg_match('~^' . self::PATH_REGEX . '$~', $path))
		{
			throw new InvalidAssetException(\sprintf("Invalid asset path: '%s'", $path));
		}

		$this->namespace = $namespace;
		$this->path = $path;
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
	 * @param string|Asset $assetPath
	 */
	public static function create ($assetPath) : self
	{
		if ($assetPath instanceof self)
		{
			return $assetPath;
		}

		if (\is_string($assetPath))
		{
			if (\preg_match('~/\\.{1,2}/~', $assetPath))
			{
				throw new InvalidAssetException(\sprintf(
					"No /./ or /../ allow in asset path: '%s'.",
					$assetPath,
				));
			}

			$parts = \explode("/", $assetPath, 2);

			if (2 !== \count($parts))
			{
				throw new InvalidAssetException(\sprintf(
					"Invalid asset path: '%s'. Must be '@namespace/path'",
					$assetPath,
				));
			}

			if ("@" !== $parts[0][0])
			{
				throw new InvalidAssetException(\sprintf(
					"Invalid asset path, namespace must start with @: '%s'. Must be '@namespace/path'",
					$assetPath,
				));
			}


			return new self(
				\substr($parts[0], 1),
				$parts[1],
			);
		}

		throw new UnexpectedTypeException($assetPath, "string or " . self::class);
	}


	/**
	 * Transforms the asset back to an asset path
	 */
	public function toAssetPath () : string
	{
		return "@{$this->namespace}/{$this->path}";
	}


	/**
	 */
	public function getExtension () : ?string
	{
		// cache extension
		if (null === $this->extension)
		{
			$this->extension = \pathinfo($this->path, \PATHINFO_EXTENSION);
		}

		return $this->extension;
	}
}
