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
					->scalarPrototype()->end()
				->end()
			->end();

		return $tree;
	}
}
