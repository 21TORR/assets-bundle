<?php declare(strict_types=1);

namespace Torr\Assets\Routing;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Asset\StoredAsset;
use Torr\Assets\Manager\AssetsManager;

final class AssetUrlGenerator
{
	private AssetsManager $assetsManager;
	private KernelInterface $kernel;
	private UrlGeneratorInterface $router;

	/**
	 */
	public function __construct (
		AssetsManager $assetsManager,
		KernelInterface $kernel,
		UrlGeneratorInterface $router
	)
	{
		$this->assetsManager = $assetsManager;
		$this->kernel = $kernel;
		$this->router = $router;
	}


	/**
	 * Returns the url to the given asset
	 */
	public function getUrl (Asset $asset) : string
	{
		$storedAsset = $this->assetsManager->getAssetMap()->get($asset->toAssetPath());

		if ($storedAsset instanceof StoredAsset && !$this->kernel->isDebug())
		{
			[$namespace, $path] = explode("/", \ltrim($storedAsset->getStoredFilePath(), "/"), 2);
		}
		else
		{
			$namespace = $asset->getNamespace();
			$path = $asset->getPath();
		}

		return $this->router->generate(
			AssetsRouteLoader::ROUTE_NAME,
			[
				'namespace' => $namespace,
				'path' => $path,
			]
		);
	}
}
