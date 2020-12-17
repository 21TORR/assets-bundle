<?php declare(strict_types=1);

namespace Torr\Assets\Container;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Torr\Assets\Namespaces\NamespaceRegistry;

/**
 * Registers a single asset namespace.
 * Can be used in bundles to register custom namespaces.
 *
 * @api
 */
final class RegisterAssetNamespaceCompilerPass implements CompilerPassInterface
{
	private string $name;
	private string $path;

	public function __construct (string $name, string $path)
	{
		$this->name = $name;
		$this->path = $path;
	}

	/**
	 * @inheritDoc
	 */
	public function process (ContainerBuilder $container) : void
	{
		$container->getDefinition(NamespaceRegistry::class)
			->addMethodCall("register", [$this->name, $this->path]);
	}
}
