<?php declare(strict_types=1);

namespace Torr\Assets\Html;

use Torr\Assets\Asset\Asset;
use Torr\Assets\File\FileTypeRegistry;
use Torr\Assets\Routing\AssetUrlGenerator;
use Torr\HtmlBuilder\Builder\HtmlBuilder;

final class AssetHtmlIncluder
{
	private HtmlBuilder $htmlBuilder;
	private AssetUrlGenerator $assetUrlGenerator;
	private FileTypeRegistry $fileTypeRegistry;

	/**
	 */
	public function __construct (
		AssetUrlGenerator $assetUrlGenerator,
		FileTypeRegistry $fileTypeRegistry
	)
	{
		$this->htmlBuilder = new HtmlBuilder();
		$this->assetUrlGenerator = $assetUrlGenerator;
		$this->fileTypeRegistry = $fileTypeRegistry;
	}


	/**
	 * Generates links to all files
	 *
	 * @param string[] $assetPaths
	 * @return string
	 */
	public function generateHtmlIncludeCodeForAssets (array $assetPaths) : string
	{
		return \implode(
			"",
			\array_map(
				fn (string $asset) => $this->createIncludeElement(Asset::create($asset)),
				$assetPaths
			)
		);
	}


	/**
	 * Generates the html link
	 */
	private function createIncludeElement (Asset $asset) : string
	{
		$fileType = $this->fileTypeRegistry->getFileType($asset);
		$includeTag = $fileType->createHtmlIncludeElement($this->assetUrlGenerator->getUrl($asset), []);

		return $this->htmlBuilder->build($includeTag);
	}
}
