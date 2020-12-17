<?php declare(strict_types=1);

namespace Torr\Assets\Storage;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Torr\Assets\Namespaces\NamespaceRegistry;

final class AssetDumper
{
	private AssetStorage $storage;
	private NamespaceRegistry $namespaceRegistry;
	private string $publicDir;
	private Filesystem $filesystem;

	/**
	 */
	public function __construct (
		AssetStorage $storage,
		NamespaceRegistry $namespaceRegistry,
		Filesystem $filesystem,
		string $publicDir
	)
	{
		$this->storage = $storage;
		$this->namespaceRegistry = $namespaceRegistry;
		$this->publicDir = \rtrim($publicDir, "/");
		$this->filesystem = $filesystem;
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
				$targetPath = "{$this->publicDir}/{$this->storage->getOutputDir()}/{$file->getRelativePathname()}";
				$dumpedFiles[] = $file->getRelativePathname();

				//$this->filesystem->copy($file->getPathname(), $targetPath);
				dump($targetPathS);
			}

			return $dumpedFiles;
		}
		catch (DirectoryNotFoundException $exception)
		{
			// ignore missing base directories
			return [];
		}
	}
}
