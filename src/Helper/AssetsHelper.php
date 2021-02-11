<?php declare(strict_types=1);

namespace Torr\Assets\Helper;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Asset\StoredAsset;
use Torr\Assets\Dependency\DependencyHelper;
use Torr\Assets\File\FileTypeRegistry;
use Torr\Assets\Manager\AssetsManager;
use Torr\Assets\Routing\AssetsRouteLoader;
use Torr\Assets\Storage\AssetStorage;

/**
 * Convenience wrapper around commonly used asset functions.
 * Your project should probably only ever use this class directly.
 *
 * @api
 */
final class AssetsHelper
{
	/** @required */
	public DependencyHelper $dependencyHelper;

	private AssetsManager $assetsManager;

	private FileTypeRegistry $fileTypeRegistry;

	private KernelInterface $kernel;

	private AssetStorage $assetStorage;

	private UrlGeneratorInterface $router;

	/**
	 */
	public function __construct (
		FileTypeRegistry $fileTypeRegistry,
		AssetsManager $assetsManager,
		KernelInterface $kernelInterface,
		AssetStorage $assetStorage,
		UrlGeneratorInterface $router,
	)
	{
		$this->fileTypeRegistry = $fileTypeRegistry;
		$this->assetsManager = $assetsManager;
		$this->kernel = $kernelInterface;
		$this->assetStorage = $assetStorage;
		$this->router = $router;
	}

	/**
	 * Returns the embed code for the given asset(s)
	 *
	 * @param array|string $value
	 */
	public function embed ($value) : string
	{
		$returnValue = "";

		if (is_array($value))
		{
			foreach ($value as $item)
			{
				$returnValue .= $this->embed($item);
			}
		}
		else
		{
			$asset = Asset::create($value);

			if ($asset->getExtension() === "js" && $this->dependencyHelper->isCollectionAvailable($value))
			{
				$returnValue .= $this->dependencyHelper->getJavaScriptCollectionSnippet($value);
			}
			else
			{
				$storedAsset = $this->assetsManager->getAssetMap()->get($value);
				$fileType = $this->fileTypeRegistry->getFileType($storedAsset->getAsset());
				$path = $this->buildUrl($storedAsset);
				$returnValue .= $fileType->getEmbedCode($path);
			}
		}

		return $returnValue;
	}


	/**
	 * Returns the url to the given asset
	 */
	public function getUrl (string $value) : string
	{
		$storedAsset = $this->assetsManager->getAssetMap()->get($value);
		return $this->buildUrl($storedAsset);
	}

	/**
	 * Build the url to the given StoredAsset
	 */
	public function buildUrl (StoredAsset $asset) : string
	{
		// TODO build full url e.g. https://example.com/{$this->path}
		return $this->getPath($asset);
	}

	/**
	 * Returns the path of the given StoredAsset
	 */
	public function getPath (StoredAsset $asset) : string
	{
		// in dev/debug mode use dynamic route
		// in prod mode use static file
		if ($this->kernel->isDebug())
		{
			return $this->router->generate(
				AssetsRouteLoader::ROUTE_NAME,
				[
					'namespace' => $asset->getAsset()->getNamespace(),
					'path' => $asset->getAsset()->getPath(),
				]
			);
		}

		return "/{$this->assetStorage->getOutputDir()}/{$asset->getStoredFilePath()}";
	}
}
