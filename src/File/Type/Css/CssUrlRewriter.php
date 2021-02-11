<?php

namespace Torr\Assets\File\Type\Css;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\Asset\InvalidAssetException;
use Torr\Assets\Helper\AssetsHelper;
use Torr\Assets\Manager\AssetsManager;
use Torr\Assets\Routing\AssetsRouteLoader;
use Torr\Assets\Storage\AssetStorage;
use function Symfony\Component\String\s;

class CssUrlRewriter
{
	/** @var array<string, Asset> */
	private array $assets = [];

	private AssetsManager $assetsManager;

	private AssetsHelper $assetsHelper;

	private AssetStorage $assetStorage;

	private UrlGeneratorInterface $router;

	/**
	 */
	public function __construct (
		AssetStorage $assetStorage,
		UrlGeneratorInterface $router,
		AssetsManager $assetsManager,
		AssetsHelper $assetsHelper
	)
	{
		$this->assetStorage = $assetStorage;
		$this->router = $router;
		$this->assetsManager = $assetsManager;
		$this->assetsHelper = $assetsHelper;
	}

	/**
	 * Find and replace namespaces in $content like url("@app/css/app.css") to url("https://example.com/assets/app/css/app.css") if "@app/css/app.css" is defined
	 */
	public function rewrite (string $content) : string
	{
		$this->findAssets($content);
		return $this->replaceNamespace($content);
	}

	/**
	 * Find in $content every used namespace like url("@app/css/app.css")
	 */
	private function findAssets (string $content) : void
	{
		preg_match_all('~url\\(\\s*(?<path>.*?)\\s*\\)~i', $content, $matches);

		foreach ($matches['path'] as $item)
		{
			$path = s($item)->replace('"', '')->replace("'", "")->toString();
			try
			{
				$this->assets[$path] = Asset::create($path);
			}
			catch (InvalidAssetException $exception)
			{
			}
		}
	}

	/**
	 * replace founded namespaces from findAssets() with path to file
	 */
	private function replaceNamespace (string $content) : string
	{
		$stringContent = s($content);

		foreach ($this->assets as $key => $item)
		{
			$map = $this->assetsManager->getAssetMap();
			$asset = $map->get($key);

			if (null == $asset)
			{
				continue;
			}

			$fullPath = $this->assetsHelper->buildUrl($asset);
			$stringContent = $stringContent->replace($key, $fullPath);
		}

		return $stringContent->toString();
	}
}
