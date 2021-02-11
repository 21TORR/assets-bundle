<?php

namespace Torr\Assets\Dependency;

use Torr\Assets\Asset\Asset;
use Torr\Assets\Asset\AssetDependency;
use Torr\Assets\Storage\AssetDependencyCollection;
use Torr\Assets\Manager\AssetsManager;
use Torr\Assets\Namespaces\NamespaceRegistry;

class DependencyRegistration
{
	/** @var AssetDependencyCollection[] */
	private $dependencyCollection;

	private NamespaceRegistry $namespaceRegistry;

	private AssetsManager $assetsManager;

	/**
	 */
	public function __construct (
		NamespaceRegistry $namespaceRegistry,
		AssetsManager $assetsManager
	)
	{
		$this->dependencyCollection = [];
		$this->namespaceRegistry = $namespaceRegistry;
		$this->assetsManager = $assetsManager;
	}

	/**
	 */
	public function register () : void
	{
		foreach ($this->namespaceRegistry->getNamespaces() as $namespace)
		{
			$path = $this->namespaceRegistry->getAssetFilePath(Asset::create("@{$namespace}/js/_dependencies.json"));
			$this->importFile($namespace, $path);
		}

		// save in cache
		$this->assetsManager->setDependencyCollection($this->dependencyCollection);
	}

	/**
	 */
	public function importFile (string $namespace, string $filePath) : void
	{
		if (!is_file($filePath))
		{
			return;
		}

		$map = \json_decode(file_get_contents($filePath), true);

		if (null !== $map)
		{
			$this->readJson($namespace, $map);
		}
	}

	/**
	 */
	public function readJson (string $namespace, array $data) : void
	{
		// key => mode (e.g. legacy or modern)
		// item => array data
		foreach ($data as $mode => $item)
		{
			// key => js name
			// item => array data
			foreach ($item as $jsName => $subItem)
			{
				$collectionPath = "@{$namespace}/js/{$jsName}.js";

				if (!array_key_exists($collectionPath, $this->dependencyCollection))
				{
					$this->dependencyCollection[$collectionPath] = new AssetDependencyCollection($namespace, "js/{$jsName}.js");
				}

				// key => index
				// item => js-path
				foreach ($subItem as $subsubKey => $subsubItem)
				{
					$assetMap = $this->assetsManager->getAssetMap();
					$storedAsset = $assetMap->get("@{$namespace}/js/{$subsubItem}");

					if (null === $storedAsset)
					{
						continue;
					}

					$assetDependency = new AssetDependency($storedAsset);

					if ($mode === "modern")
					{
						$assetDependency->setModern(true);
					}

					if ($mode === "legacy")
					{
						$assetDependency->setLegacy(true);
					}

					$this->dependencyCollection[$collectionPath]->addToCollection($assetDependency);
				}
			}
		}
	}
}
