<?php declare(strict_types=1);

namespace Torr\Assets\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Controller\EmbedController;
use Torr\Assets\Storage\AssetStorage;

final class AssetsRouteLoader extends Loader
{
	public const ROUTE_NAME = "_assets.embed";
	private AssetStorage $assetStorage;


	/**
	 */
	public function __construct (AssetStorage $assetStorage)
	{
		$this->assetStorage = $assetStorage;
	}


	/**
	 * @inheritDoc
	 */
	public function load ($resource, string $type = null) : RouteCollection
	{
		$collection = new RouteCollection();
		$output = $this->assetStorage->getOutputDir();

		$collection->add(self::ROUTE_NAME, new Route(
			"/{$output}/{namespace}/{path}",
			[
				"_controller" => EmbedController::class . "::embed",
			],
			[
				"namespace" => Asset::NAMESPACE_REGEX,
				"path" => Asset::PATH_REGEX,
			],
		));

		return $collection;
	}


	/**
	 * @inheritDoc
	 */
	public function supports ($resource, string $type = null)
	{
		return "assets-routes" === $type;
	}
}
