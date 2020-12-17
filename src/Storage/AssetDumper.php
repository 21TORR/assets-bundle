<?php declare(strict_types=1);

namespace Torr\Assets\Storage;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Torr\Assets\Asset\Asset;
use Torr\Assets\File\FileLoader;
use Torr\Assets\Namespaces\NamespaceRegistry;

final class AssetDumper
{
	private AssetStorage $storage;
	private NamespaceRegistry $namespaceRegistry;
	private string $publicDir;
	private Filesystem $filesystem;
	private FileLoader $fileLoader;

	/**
	 */
	public function __construct (
		AssetStorage $storage,
		NamespaceRegistry $namespaceRegistry,
		Filesystem $filesystem,
		FileLoader $fileLoader,
		string $publicDir
	)
	{
		$this->storage = $storage;
		$this->namespaceRegistry = $namespaceRegistry;
		$this->publicDir = \rtrim($publicDir, "/");
		$this->filesystem = $filesystem;
		$this->fileLoader = $fileLoader;
	}


	/**
	 * Clears (and removes) the dump directory
	 */
	public function clearDumpDirectory () : void
	{
		$this->filesystem->remove($this->getDumpDir());
	}


	/**
	 */
	public function dumpNamespace (string $namespace) : array
	{
		$path = $this->namespaceRegistry->getNamespacePath($namespace);

		try
		{
			$finder = Finder::create()
				->in($path)
				->files()
				->ignoreDotFiles(true)
				->ignoreUnreadableDirs()
			;

			$dumpedFiles = [];

			foreach ($finder as $file)
			{
				$targetPath = "{$this->getDumpDir()}/{$namespace}/{$file->getRelativePathname()}";

				$content = $this->fileLoader->loadFile(new Asset($namespace, $file->getRelativePathname()), FileLoader::MODE_PRODUCTION);
				$this->filesystem->dumpFile($targetPath, $content);

				$dumpedFiles[] = $file->getRelativePathname();
			}

			return $dumpedFiles;
		}
		catch (DirectoryNotFoundException $exception)
		{
			// ignore missing base directories
			return [];
		}
	}


	/**
	 * Returns the full path to the dump dir
	 */
	private function getDumpDir () : string
	{
		return "{$this->publicDir}/{$this->storage->getOutputDir()}";
	}
}
