<?php declare(strict_types=1);

namespace Torr\Assets\Routing;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Asset\StoredAsset;
use Torr\Assets\Manager\AssetsManager;
use Torr\Assets\Storage\AssetStorage;

final class AssetUrlGenerator
{
	private AssetsManager $assetsManager;
	private KernelInterface $kernel;
	private UrlGeneratorInterface $router;
	private AssetStorage $assetStorage;

	/**
	 */
	public function __construct (
		AssetsManager $assetsManager,
		KernelInterface $kernel,
		UrlGeneratorInterface $router,
		AssetStorage $assetStorage
	)
	{
		$this->assetsManager = $assetsManager;
		$this->kernel = $kernel;
		$this->router = $router;
		$this->assetStorage = $assetStorage;
	}


	/**
	 * Returns the url to the given asset
	 */
	public function getUrl (Asset $asset) : string
	{
		$storedAsset = $this->assetsManager->getAssetMap()->get($asset->toAssetPath());

		if ($storedAsset instanceof StoredAsset)
		{
			$path = "/{$this->assetStorage->getOutputDir()}/{$storedAsset->getStoredFilePath()}";
		}
		else
		{
			$path = $this->router->generate(
				AssetsRouteLoader::ROUTE_NAME,
				[
					'namespace' => $asset->getNamespace(),
					'path' => $asset->getPath(),
				]
			);
		}

		return $path;
	}
}
