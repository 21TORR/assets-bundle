<?php declare(strict_types=1);

namespace Torr\Assets;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Torr\Assets\DependencyInjection\AssetsBundleConfiguration;
use Torr\BundleHelpers\Bundle\ConfigurableBundleExtension;

final class AssetsBundle extends Bundle
{
	/**
	 * @inheritDoc
	 */
	public function getContainerExtension () : ExtensionInterface
	{
		return new ConfigurableBundleExtension(
			$this,
			new AssetsBundleConfiguration(),
			static function (array $config, ContainerBuilder $container) : void
			{
			}
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getPath () : string
	{
		return \dirname(__DIR__);
	}
}
