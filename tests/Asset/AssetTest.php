<?php declare(strict_types=1);

namespace Tests\Torr\Assets\Asset;

use PHPUnit\Framework\TestCase;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\Asset\InvalidAssetException;
use Torr\Rad\Exception\UnexpectedTypeException;

final class AssetTest extends TestCase
{
	/**
	 */
	public function provideCreateFromAssetPath () : iterable
	{
		yield [
			"@app/test.jpg",
			"app",
			"test.jpg",
		];

		yield [
			"@app-123/test.jpg",
			"app-123",
			"test.jpg",
		];

		yield [
			"@app_123/test.jpg",
			"app_123",
			"test.jpg",
		];

		yield [
			"@app/test/example.jpg",
			"app",
			"test/example.jpg",
		];

		yield [
			"@app/sub.end/example.jpg",
			"app",
			"sub.end/example.jpg",
		];
	}


	/**
	 * @dataProvider provideCreateFromAssetPath
	 */
	public function testCreateFromAssetPath (string $assetPath, string $expectedNamespace, string $expectedPath) : void
	{
		$asset = Asset::create($assetPath);

		self::assertSame($expectedNamespace, $asset->getNamespace());
		self::assertSame($expectedPath, $asset->getPath());

		// should produce same asset path
		self::assertSame($assetPath, $asset->toAssetPath());
	}

	/**
	 *
	 */
	public function testCreateFromAsset () : void
	{
		$asset = new Asset("app", "example.jpg");
		$second = Asset::create($asset);

		self::assertSame($asset, $second);
	}


	/**
	 */
	public function provideCreateInvalidValue () : iterable
	{
		yield "empty" => [""];
		yield "just string" => ["test"];
		yield "no @" => ["app/test.jpg"];
		yield "no path" => ["@app"];
		yield "path is no file" => ["@app/"];
		yield "duplicate slash" => ["@app//test.jpg"];
		yield "invalid namespace number" => ["@123/test.jpg"];
		yield "invalid namespace underscore" => ["@_app/test.jpg"];
		yield "slash at end of path" => ["@app/test/"];
		yield "no double dots" => ["@app/../example.jpg"];
		yield "no single dots" => ["@app/./example.jpg"];
	}


	/**
	 * @dataProvider provideCreateInvalidValue
	 */
	public function testCreateInvalidValue ($input) : void
	{
		$this->expectException(InvalidAssetException::class);
		Asset::create($input);
	}


	/**
	 */
	public function provideCreateInvalidType () : iterable
	{
		yield [3];
		yield [false];
		yield [true];
		yield [3.0];
		yield [null];
		yield [["test"]];
		yield [(object) ["test"]];
	}


	/**
	 * @dataProvider provideCreateInvalidType
	 */
	public function testCreateInvalidType ($input) : void
	{
		$this->expectException(UnexpectedTypeException::class);
		Asset::create($input);
	}

	/**
	 *
	 */
	public function testRoundTrip () : void
	{
		$asset = Asset::create("@test/sub/example.jpg");

		self::assertSame("test", $asset->getNamespace());
		self::assertSame("sub/example.jpg", $asset->getPath());
	}
}
