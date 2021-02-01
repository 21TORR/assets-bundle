<?php


namespace Torr\Assets\File\Type\Css;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\Asset\InvalidAssetException;
use Torr\Assets\Routing\AssetsRouteLoader;
use Torr\Assets\Storage\AssetMapCache;
use Torr\Assets\Storage\AssetStorage;
use function Symfony\Component\String\s;

class CssUrlRewriter
{
	/** @var array<string, Asset> */
	private array $assets = [];

	private AssetMapCache $assetMapCache;

	private AssetStorage $assetStorage;

	private UrlGeneratorInterface $router;

	/**
	 */
	public function __construct(AssetMapCache $assetMapCache, AssetStorage $assetStorage, UrlGeneratorInterface $router)
	{
		$this->assetMapCache = $assetMapCache;
		$this->assetStorage = $assetStorage;
		$this->router = $router;
	}

	/**
	 * Find and replace namespaces in $content like url("@app/css/app.css") to url("https://example.com/assets/app/css/app.css") if "@app/css/app.css" is defined
	 */
	public function rewrite(string $content, bool $debug) : string
	{
		$this->findAssets($content);
		return $this->replaceNamespace($content, $debug);
	}

	/**
	 * Find in $content every used namespace like url("@app/css/app.css")
	 */
	private function findAssets(string $content)
	{
		preg_match_all('~url\\(\\s*(?<path>.*?)\\s*\\)~i', $content, $matches);

		foreach($matches['path'] as $item)
		{
			$path = s($item)->replace('"', '')->replace("'", "")->toString();
			try {
				$this->assets[$path] = Asset::create($path);
			}
			catch(InvalidAssetException $exception) {}
		}
	}

	/**
	 * replace founded namespaces from findAssets() with path to file
	 */
	private function replaceNamespace(string $content, bool $debug) : string
	{
		$stringContent = s($content);

		foreach ($this->assets as $key => $item)
		{
			// in dev/debug mode use dynamic route
			// in prod mode use static file
			if($debug)
			{
				$fullPath = $this->router->generate(
					AssetsRouteLoader::ROUTE_NAME,
					[
						'namespace' => $item->getNamespace(),
						'path' => $item->getPath(),
					]
				);
			}
			else
			{
				$map = $this->assetMapCache->getAssetMap();
				$asset = $map->get($key);

				if(null === $asset)
				{
					continue;
				}

				$fullPath = "/{$this->assetStorage->getOutputDir()}/{$asset->getStoredFilePath()}";
			}

			$stringContent = $stringContent->replace($key, $fullPath);
		}

		return $stringContent->toString();
	}
}