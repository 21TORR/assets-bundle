<?php declare(strict_types=1);

namespace Torr\Assets\Namespaces;

use Torr\Assets\Exception\Namespaces\DuplicateNamespaceException;
use Torr\Assets\Exception\Namespaces\InvalidNamespacePathException;

/**
 * The namespace registry contains all registered namespaces with the paths to the assets
 */
final class NamespaceRegistry implements \IteratorAggregate, \Countable
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
	 * @inheritDoc
	 */
	public function getIterator () : \Traversable
	{
		\uksort($this->namespaces, "strnatcasecmp");
		return new \ArrayIterator($this->namespaces);
	}

	/**
	 * @inheritDoc
	 */
	public function count () : int
	{
		return \count($this->namespaces);
	}


	/**
	 * Returns whether the given path is valid
	 */
	private function isValidPath (string $path) : bool
	{
		return "/" === $path[0];
	}
}
