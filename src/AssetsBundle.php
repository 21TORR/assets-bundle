<?php declare(strict_types=1);

namespace Torr\Assets;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Torr\Assets\DependencyInjection\AssetsBundleConfiguration;
use Torr\Assets\File\Type\FileType;
use Torr\Assets\Namespaces\NamespaceRegistry;
use Torr\Assets\Storage\AssetStorage;
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
				$container->getDefinition(AssetStorage::class)
					->setArgument('$outputDir', $config["output_dir"]);

				$container->getDefinition(NamespaceRegistry::class)
					->setArgument('$projectNamespaces', $config["namespaces"]);
			}
		);
	}


	/**
	 * @inheritDoc
	 */
	public function build (ContainerBuilder $container) : void
	{
		parent::build($container);

		$container->registerForAutoconfiguration(FileType::class)
			->addTag("assets.file-type");
	}


	/**
	 * @inheritDoc
	 */
	public function getPath () : string
	{
		return \dirname(__DIR__);
	}
}
