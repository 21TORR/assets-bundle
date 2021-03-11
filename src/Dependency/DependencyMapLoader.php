<?php
declare(strict_types=1);

namespace Torr\Assets\Dependency;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Asset\Dependency\AssetDependency;
use Torr\Assets\Asset\Dependency\DependencyMap;
use Torr\Assets\Namespaces\NamespaceRegistry;

final class DependencyMapLoader implements CacheClearerInterface
{
	private const CACHE_KEY = "21torr.assets.dependency_map";
	private NamespaceRegistry $namespaceRegistry;
	private CacheInterface $cache;
	private LoggerInterface $logger;
	private bool $isDebug;
	private ?DependencyMap $map = null;

	/**
	 */
	public function __construct (
		NamespaceRegistry $namespaceRegistry,
		CacheInterface $cache,
		LoggerInterface $logger,
		bool $isDebug
	)
	{
		$this->namespaceRegistry = $namespaceRegistry;
		$this->cache = $cache;
		$this->logger = $logger;
		$this->isDebug = $isDebug;
	}


	/**
	 */
	public function load () : DependencyMap
	{
		if (null === $this->map)
		{
			$this->map = !$this->isDebug
				? $this->cache->get(self::CACHE_KEY, fn () => $this->generate())
				: $this->generate();
		}

		return $this->map;
	}


	/**
	 */
	private function generate () : DependencyMap
	{
		$map = new DependencyMap();

		foreach ($this->namespaceRegistry->getNamespaces() as $namespace)
		{
			$path = $this->namespaceRegistry->getAssetFilePath(new Asset($namespace, "js/_dependencies.json"));
			$this->importDependenciesFile($map, $namespace, $path);
		}

		return $map;
	}

	/**
	 */
	private function importDependenciesFile (DependencyMap $dependencyMap, string $namespace, string $dependenciesFile) : void
	{
		if (!\is_file($dependenciesFile) || !\is_readable($dependenciesFile))
		{
			return;
		}

		try
		{
			$data = \json_decode(\file_get_contents($dependenciesFile), true, 512, \JSON_THROW_ON_ERROR);

			if (!\is_array($data))
			{
				$this->logger->error("Invalid dependencies content in '{filePath}': not an array", [
					"filePath" => $dependenciesFile,
				]);

				return;
			}

			foreach ($data as $entryFilePath => $dependencyEntries)
			{
				if (!$this->isValid($dependencyEntries))
				{
					$this->logger->error("Invalid dependencies content in '{filePath}': invalid entries", [
						"filePath" => $dependenciesFile,
					]);

					return;
				}

				$entryAsset = new Asset($namespace, "js/{$entryFilePath}");

				foreach ($dependencyEntries["modern"] as $entry)
				{
					$dependencyMap->registerDependency(
						$entryAsset,
						new AssetDependency(
							new Asset($namespace, "js/{$entry}"),
							["type" => "module"]
						)
					);
				}

				foreach ($dependencyEntries["legacy"] as $entry)
				{
					$dependencyMap->registerDependency(
						$entryAsset,
						new AssetDependency(
							new Asset($namespace, "js/{$entry}"),
							["nomodule" => true]
						)
					);
				}
			}
		}
		catch (\JsonException $exception)
		{
			$this->logger->error("Can't parse dependencies JSON at path {filePath}: {message}", [
				"filePath" => $dependenciesFile,
				"message" => $exception->getMessage(),
				"exception" => $exception,
			]);
		}
	}


	/**
	 * Returns whether the entries entry is valid
	 *
	 * @param mixed $entries
	 */
	private function isValid ($entries) : bool
	{
		if (!\is_array($entries) || !isset($entries["modern"], $entries["legacy"]))
		{
			return false;
		}


		foreach ($entries as $values)
		{
			if (!\is_array($values))
			{
				return false;
			}

			foreach ($values as $value)
			{
				if (!\is_string($value))
				{
					return false;
				}
			}
		}

		return true;
	}


	/**
	 * Clears the cache
	 */
	public function clear(string $cacheDir)
	{
		return $this->cache->delete(self::CACHE_KEY);
	}

	/**
	 * Refreshes the dependencies map
	 */
	public function refresh () : void
	{
		$this->clear("");
		$this->load();
	}
}
