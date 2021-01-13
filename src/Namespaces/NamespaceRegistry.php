<?php declare(strict_types=1);

namespace Torr\Assets\Namespaces;

use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\Namespaces\DuplicateNamespaceException;
use Torr\Assets\Exception\Namespaces\InvalidNamespacePathException;
use Torr\Assets\Exception\Namespaces\UnknownNamespaceException;

/**
 * The namespace registry contains all registered namespaces with the paths to the assets
 */
final class NamespaceRegistry
{
	/** @var array<string, string> */
	private array $namespaces = [];


	/**
	 */
	public function __construct (array $projectNamespaces = [], ?string $projectDir = null)
	{
		if (empty($projectNamespaces))
		{
			return;
		}

		if (null === $projectDir)
		{
			throw new InvalidNamespacePathException("Can't add initial project namespaces without project dir.");
		}

		$projectDir = \rtrim($projectDir, "/");

		foreach ($projectNamespaces as $name => $path)
		{
			$this->register(
				$name,
				"{$projectDir}/" . \ltrim($path, "/")
			);
		}
	}


	/**
	 * Registers a new asset namespace
	 *
	 * @throws InvalidNamespacePathException
	 * @throws DuplicateNamespaceException
	 */
	public function register (string $name, string $path) : void
	{
		if (!$this->isValidPath($path))
		{
			throw new InvalidNamespacePathException(\sprintf(
				"Invalid namespace path: '%s'. Namespace paths must be absolute and start with a '/'.",
				$path
			));
		}

		if (\array_key_exists($name, $this->namespaces))
		{
			throw new DuplicateNamespaceException(\sprintf(
				"Can't register namespace '%s' with path '%s', as it is already registered with path '%s'",
				$name,
				$path,
				$this->namespaces[$name]
			));
		}

		$this->namespaces[$name] = \rtrim($path, "/");
	}


	/**
	 * Returns whether the given path is valid
	 */
	private function isValidPath (string $path) : bool
	{
		return "/" === $path[0];
	}


	/**
	 * Returns the storage path to the namespace.
	 *
	 * @throws UnknownNamespaceException
	 */
	public function getNamespacePath (string $namespace) : string
	{
		$path = $this->namespaces[$namespace] ?? null;

		if (null === $path)
		{
			throw new UnknownNamespaceException(\sprintf(
				"Unknown namespace '%s'. Did you register it?",
				$namespace
			));
		}

		return $path;
	}


	/**
	 * Returns the file path to the given asset.
	 *
	 * @throws UnknownNamespaceException
	 */
	public function getAssetFilePath (Asset $asset) : string
	{
		return "{$this->getNamespacePath($asset->getNamespace())}/{$asset->getPath()}";
	}


	/**
	 * Returns all registered namespaces
	 */
	public function getNamespaces () : array
	{
		return \array_keys($this->namespaces);
	}
}
