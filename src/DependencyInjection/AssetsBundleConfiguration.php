<?php declare(strict_types=1);

namespace Torr\Assets\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class AssetsBundleConfiguration implements ConfigurationInterface
{
	/**
	 * @inheritDoc
	 */
	public function getConfigTreeBuilder ()
	{
		$tree = new TreeBuilder("assets");

		$tree->getRootNode()
			->children()
				->arrayNode("namespaces")
					->useAttributeAsKey("name")
					->info("The namespaces of the app. These are relative to the project dir.")
					->scalarPrototype()->end()
				->end()
				->scalarNode("output_dir")
					->defaultValue("assets")
					->info("The path where the files should be stored. Relative to the public dir.")
				->end()
			->end();

		return $tree;
	}
}
