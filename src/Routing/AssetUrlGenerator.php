<?php declare(strict_types=1);

namespace Torr\Assets\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Manager\AssetsManager;
use Torr\Assets\Storage\AssetStorageMap;

final class AssetUrlGenerator
{
	private AssetsManager $assetsManager;
	private UrlGeneratorInterface $router;
	private bool $isDebug;

	/**
	 */
	public function __construct (
		AssetsManager $assetsManager,
		UrlGeneratorInterface $router,
		bool $isDebug,
	)
	{
		$this->assetsManager = $assetsManager;
		$this->router = $router;
		$this->isDebug = $isDebug;
	}


	/**
	 * Returns the url to the given asset
	 */
	public function getUrl (Asset $asset, ?AssetStorageMap $storageMap = null) : string
	{
		if (null === $storageMap)
		{
			$storageMap = $this->assetsManager->getStorageMap();
		}

		$toEmbed = $asset;

		if (!$this->isDebug)
		{
			$toEmbed = $storageMap->get($asset);
		}

		return $this->router->generate(
			AssetsRouteLoader::ROUTE_NAME,
			[
				'namespace' => $toEmbed->getNamespace(),
				'path' => $toEmbed->getPath(),
			],
		);
	}
}
