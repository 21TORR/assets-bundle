<?php declare(strict_types=1);

namespace Torr\Assets\Helper;

use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\File\NotEmbeddableAssetException;
use Torr\Assets\File\FileLoader;
use Torr\Assets\File\FileTypeRegistry;
use Torr\Assets\File\Type\ProcessableFileTypeInterface;
use Torr\Assets\Html\AssetHtmlIncluder;
use Torr\Assets\Routing\AssetUrlGenerator;

/**
 * Convenience wrapper around commonly used asset functions.
 * Your project should probably only ever use this class directly.
 *
 * @api
 */
final class AssetsHelper
{
	private FileTypeRegistry $fileTypeRegistry;
	private FileLoader $fileLoader;
	private AssetHtmlIncluder $assetHtmlIncluder;
	private AssetUrlGenerator $assetUrlGenerator;

	/**
	 */
	public function __construct (
		FileTypeRegistry $fileTypeRegistry,
		FileLoader $fileLoader,
		AssetHtmlIncluder $assetHtmlIncluder,
		AssetUrlGenerator $assetUrlGenerator
	)
	{
		$this->fileTypeRegistry = $fileTypeRegistry;
		$this->fileLoader = $fileLoader;
		$this->assetHtmlIncluder = $assetHtmlIncluder;
		$this->assetUrlGenerator = $assetUrlGenerator;
	}

	/**
	 * Returns the embed code for the given asset(s)
	 */
	public function embed (string $assetPath) : string
	{
		$asset = Asset::create($assetPath);
		$fileType = $this->fileTypeRegistry->getFileType($asset);

		if (!$fileType instanceof ProcessableFileTypeInterface)
		{
			throw new NotEmbeddableAssetException(\sprintf(
				"File '%s' of type '%s' is not embeddable.",
				$assetPath,
				\get_class($fileType)
			));
		}

		return $this->fileLoader->loadUnprocessed($asset);
	}

	/**
	 * @param string[] $assetPaths
	 */
	public function includeAssetsInHtml (array $assetPaths) : string
	{
		return $this->assetHtmlIncluder->generateHtmlIncludeCodeForAssets($assetPaths);
	}


	/**
	 * Returns the url to the given asset
	 */
	public function getUrl (string $assetPath) : string
	{
		return $this->assetUrlGenerator->getUrl(Asset::create($assetPath));
	}
}
