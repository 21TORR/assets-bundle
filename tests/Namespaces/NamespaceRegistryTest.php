<?php declare(strict_types=1);

namespace Tests\Torr\Assets\Namespaces;

use PHPUnit\Framework\TestCase;
use Torr\Assets\Exception\Namespaces\DuplicateNamespaceException;
use Torr\Assets\Exception\Namespaces\InvalidNamespacePathException;
use Torr\Assets\Namespaces\NamespaceRegistry;

final class NamespaceRegistryTest extends TestCase
{
	/**
	 */
	public function provideProjectDirPrefix () : iterable
	{
		yield "project dir no slash" => [
			[
				"noslash" => "/project/dir/noslash",
				"slashend" => "/project/dir/slashend",
				"slashstart" => "/project/dir/slashstart",
				"slashes" => "/project/dir/slashes",
			],
			"/project/dir",
			[
				"noslash" => "noslash",
				"slashend" => "slashend/",
				"slashstart" => "/slashstart",
				"slashes" => "/slashes/",
			],
		];

		yield "project dir with slash" => [
			[
				"noslash" => "/project/dir/noslash",
				"slashend" => "/project/dir/slashend",
				"slashstart" => "/project/dir/slashstart",
				"slashes" => "/project/dir/slashes",
			],
			"/project/dir/",
			[
				"noslash" => "noslash",
				"slashend" => "slashend/",
				"slashstart" => "/slashstart",
				"slashes" => "/slashes/",
			],
		];

		yield "dir only slash" => [
			["app" => "/project/dir"],
			"/project/dir",
			["app" => "/"],
		];
	}


	/**
	 * @dataProvider provideProjectDirPrefix
	 */
	public function testProjectDirPrefix (array $expected, string $projectDir, array $initialPaths) : void
	{
		$registry = new NamespaceRegistry($initialPaths, $projectDir);
		$actual = [];

		foreach ($registry->getNamespaces() as $namespace)
        {
            $actual[$namespace] = $registry->getNamespacePath($namespace);
        }

		self::assertEqualsCanonicalizing($expected, $actual);
	}


	/**
	 *
	 */
	public function testInvalidProjectDir () : void
	{
		$this->expectException(InvalidNamespacePathException::class);
		new NamespaceRegistry(["a" => "test"], "no/slash/at/beginning");
	}


	/**
	 *
	 */
	public function testMissingProjectDir () : void
	{
		$this->expectException(InvalidNamespacePathException::class);
		new NamespaceRegistry(["a" => "test"]);
	}


	/**
	 *
	 */
	public function testNoInitialPaths () : void
	{
		// should throw no exception
		$registry = new NamespaceRegistry();
		self::assertInstanceOf(NamespaceRegistry::class, $registry);
	}

	/**
	 *
	 */
	public function testInvalidPath () : void
	{
		$this->expectException(InvalidNamespacePathException::class);
		$registry = new NamespaceRegistry();
		$registry->register("test", "abc");
	}

	/**
	 *
	 */
	public function testDuplicateRegistration () : void
	{
		$this->expectException(DuplicateNamespaceException::class);
		$registry = new NamespaceRegistry();
		$registry->register("test", "/abc");
		$registry->register("test", "/abc2");
	}

	/**
	 *
	 */
	public function testDuplicateRegistrationWithInitial () : void
	{
		$this->expectException(DuplicateNamespaceException::class);
		$registry = new NamespaceRegistry([
			"test" => "abc",
		], "/project");
		$registry->register("test", "/abc2");
	}
}
