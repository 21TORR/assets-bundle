<?php declare(strict_types=1);

namespace Torr\Assets\Html;

use Torr\Assets\Asset\Asset;
use Torr\Assets\Asset\Dependency\AssetDependency;
use Torr\Assets\Dependency\DependencyMapLoader;
use Torr\Assets\File\FileTypeRegistry;
use Torr\Assets\Routing\AssetUrlGenerator;
use Torr\HtmlBuilder\Builder\HtmlBuilder;

final class AssetHtmlIncluder
{
	private HtmlBuilder $htmlBuilder;
	private AssetUrlGenerator $assetUrlGenerator;
	private FileTypeRegistry $fileTypeRegistry;
	private DependencyMapLoader $dependencyMapLoader;

	/**
	 */
	public function __construct (
		AssetUrlGenerator $assetUrlGenerator,
		FileTypeRegistry $fileTypeRegistry,
		DependencyMapLoader $dependencyMapLoader
	)
	{
		$this->htmlBuilder = new HtmlBuilder();
		$this->assetUrlGenerator = $assetUrlGenerator;
		$this->fileTypeRegistry = $fileTypeRegistry;
		$this->dependencyMapLoader = $dependencyMapLoader;
	}


	/**
	 * Generates links to all files
	 *
	 * @param string[] $assetPaths
	 */
	public function generateHtmlIncludeCodeForAssets (array $assetPaths) : string
	{
		$html = [];
		$map = $this->dependencyMapLoader->load();

		foreach ($assetPaths as $assetPath)
		{
			$asset = Asset::create($assetPath);
			$dependencies = $map->getDependencies($asset);

			foreach ($dependencies as $dependency)
			{
				$html[] = $this->createIncludeElement($dependency);
			}
		}

		return \implode("", $html);
	}


	/**
	 * Generates the html include element
	 */
	private function createIncludeElement (AssetDependency $dependency) : string
	{
		$asset = $dependency->getAsset();
		$url = $this->assetUrlGenerator->getUrl($asset);

		$fileType = $this->fileTypeRegistry->getFileType($asset);
		$includeTag = $fileType->createHtmlIncludeElement($url, $dependency->getAttributes());

		return $this->htmlBuilder->build($includeTag);
	}
}
