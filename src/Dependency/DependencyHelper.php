<?php

namespace Torr\Assets\Dependency;

use Torr\Assets\File\FileTypeRegistry;
use Torr\Assets\Helper\AssetsHelper;
use Torr\Assets\Manager\AssetsManager;
use Torr\Assets\Namespaces\NamespaceRegistry;

class DependencyHelper
{
	private AssetsManager $assetsManager;

	private AssetsHelper $assetsHelper;

	private FileTypeRegistry $fileTypeRegistry;

	private NamespaceRegistry $namespaceRegistry;

	/**
	 */
	public function __construct (
		FileTypeRegistry $fileTypeRegistry,
		AssetsManager $assetsManager,
		NamespaceRegistry $namespaceRegistry,
		AssetsHelper $assetsHelper
	)
	{
		$this->fileTypeRegistry = $fileTypeRegistry;
		$this->assetsManager = $assetsManager;
		$this->namespaceRegistry = $namespaceRegistry;
		$this->assetsHelper = $assetsHelper;
	}

	/**
	 * Return HTML Snippet of including the js files
	 */
	public function getJavaScriptCollectionSnippet (string $path) : string
	{
		$jsString = "";

		if ($this->isCollectionAvailable($path))
		{
			foreach ($this->assetsManager->getCollectionByPath($path)->getCollection() as $item)
			{
				$fileType = $this->fileTypeRegistry->getFileType($item->getAsset()->getAsset());
				$path = $this->assetsHelper->buildUrl($item->getAsset());
				$parameter = [
					'modern' => $item->isModern(),
					'legacy' => $item->isLegacy()
				];
				$jsString .= $fileType->getEmbedCode($path, $parameter);
			}
		}

		return $jsString;
	}

	/**
	 * Check if path has a dependency collection
	 */
	public function isCollectionAvailable (string $path) : bool
	{
		$collection = $this->assetsManager->getCollectionByPath($path);
		return (null !== $collection);
	}
}
