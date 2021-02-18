<?php

namespace Torr\Assets\Twig;

use Torr\Assets\Helper\AssetsHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetsTwigExtension extends AbstractExtension
{
	private AssetsHelper $assetsHelper;

	/**
	 */
	public function __construct (AssetsHelper $assetsHelper)
	{
		$this->assetsHelper = $assetsHelper;
	}

	/**
	 * @inheritDoc
	 */
	public function getFunctions () : array
	{
		return [
			new TwigFunction('asset', [$this->assetsHelper, 'getUrl']),
			new TwigFunction('asset_embed', [$this->assetsHelper, 'embed'], ["is_safe" => ["html"]]),
			new TwigFunction('assets_include', [$this->assetsHelper, 'includeAssetsInHtml'], ["is_safe" => ["html"]]),
		];
	}
}
