<?php declare(strict_types=1);

namespace Torr\Assets\File\Type\Css;

use Symfony\Component\Filesystem\Path;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\Asset\InvalidAssetException;
use Torr\Assets\Routing\AssetUrlGenerator;
use Torr\Assets\Storage\AssetStorageMap;

class CssUrlRewriter
{
	private AssetUrlGenerator $assetUrlGenerator;

	/**
	 */
	public function __construct (AssetUrlGenerator $assetUrlGenerator)
	{
		$this->assetUrlGenerator = $assetUrlGenerator;
	}

	/**
	 * Find and replace namespaces in $content like url("@app/css/app.css") to url("/assets/app/css/app.css") if "@app/css/app.css" is defined
	 */
	public function rewrite (Asset $baseAsset, AssetStorageMap $storageMap, string $content) : string
	{
		return (string) \preg_replace_callback(
			'~url\\(\\s*(?<path>.*?)\\s*\\)~i',
			function (array $match) use ($storageMap, $baseAsset) : string
			{
				return $this->replaceImport($baseAsset, $storageMap, $match);
			},
			$content,
		);
	}

	/**
	 * Replaces the single import with the resolved path
	 */
	private function replaceImport (Asset $baseAsset, AssetStorageMap $storageMap, array $match) : string
	{
		$importPath = \trim($match["path"], " '\"");

		// early exit for data URLs
		if (\str_starts_with($importPath, "data:"))
		{
			return $match[0];
		}

		// if not already namespaced path, try to resolve it
		if (!\str_starts_with($importPath, "@"))
		{
			$importPath = Path::join(
				Path::getDirectory($baseAsset->toAssetPath()),
				$importPath
			);
		}

		try
		{
			$asset = Asset::create($importPath);
			return \sprintf(
				'url("%s")',
				$this->assetUrlGenerator->getUrl($asset, $storageMap, AssetUrlGenerator::FORCE_LOOKUP)
			);
		}
		catch (InvalidAssetException $exception)
		{
			// if there is an issue -> just return unchanged
			return $match[0];
		}
	}
}
