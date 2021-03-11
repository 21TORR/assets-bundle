<?php declare(strict_types=1);

namespace Torr\Assets\Storage;

use Symfony\Component\Filesystem\Filesystem;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Asset\StoredAsset;

final class AssetStorage
{
	private const HASH_ALGORITHM = "sha256";

	private Filesystem $filesystem;
	private string $storageDir;
	private string $outputDir;

	/**
	 */
	public function __construct (
		Filesystem $filesystem,
		string $publicDir,
		string $outputDir
	)
	{
		$this->filesystem = $filesystem;
		$this->outputDir = \trim($outputDir, "/");
		$this->storageDir = "{$publicDir}/{$this->outputDir}";
	}


	/**
	 * @internal
	 */
	public function getOutputDir () : string
	{
		return $this->outputDir;
	}


	/**
	 * Stores the given asset
	 */
	public function storeAsset (
		Asset $asset,
		string $content,
		bool $shouldHashFileName
	) : StoredAsset
	{
		$fileName = \basename($asset->getPath());
		$fileDir = \dirname($asset->getPath());
		$hash = \base64_encode(\hash(self::HASH_ALGORITHM, $content, true));

		if ("." !== $fileDir)
		{
			$fileDir .= "/";
		}

		if ($shouldHashFileName)
		{
			$fileName = $this->createFileNameWithHash($fileName, $hash);
		}

		$storagePath = $fileDir . $fileName;
		$targetPath = "{$this->storageDir}/{$asset->getNamespace()}/{$storagePath}";
		$this->filesystem->dumpFile($targetPath, $content);

		return new StoredAsset(
			$asset,
			$hash,
			self::HASH_ALGORITHM,
			$storagePath
		);
	}


	/**
	 * Creates the file name containing the hash
	 */
	private function createFileNameWithHash (string $fileName, string $hash) : string
	{
		$extension = $this->getExtension($fileName);
		$basename = \basename($fileName, ".{$extension}");

		$fileNameHash = \rtrim($hash, "=");
		$fileNameHash = \strtr($fileNameHash, [
			"/" => "_",
			"+" => "-",
		]);
		$fileNameHash = \substr($fileNameHash, 0, 20);

		return "{$basename}.{$fileNameHash}.{$extension}";
	}

	/**
	 * Returns the extension of the file
	 */
	private function getExtension (string $fileName) : string
	{
		$extension = \pathinfo($fileName, \PATHINFO_EXTENSION);

		// special handling for .ext.map files
		if ("map" === $extension)
		{
			$secondExtension = \pathinfo(\basename($fileName, ".{$extension}"), \PATHINFO_EXTENSION);

			if ("" !== $secondExtension)
			{
				return "{$secondExtension}.{$extension}";
			}
		}

		return $extension;
	}


	/**
	 * Clears (and removes) the storage directory
	 */
	public function clearStorage () : void
	{
		$this->filesystem->remove($this->storageDir);
	}
}
