<?php declare(strict_types=1);

namespace Torr\Assets\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use function Symfony\Component\String\u;
use Torr\Assets\File\FileTypeRegistry;
use Torr\Assets\Namespaces\NamespaceRegistry;
use Torr\Cli\Console\Style\TorrStyle;

final class AssetsDebugCommand extends Command
{
	protected static $defaultName = "21torr:assets:debug";
	private NamespaceRegistry $namespaceRegistry;
	private FileTypeRegistry $fileTypeRegistry;
	private KernelInterface $kernel;


	/**
	 * @inheritDoc
	 */
	public function __construct (
		NamespaceRegistry $namespaceRegistry,
		FileTypeRegistry $fileTypeRegistry,
		KernelInterface $kernel,
	)
	{
		parent::__construct();

		$this->namespaceRegistry = $namespaceRegistry;
		$this->fileTypeRegistry = $fileTypeRegistry;
		$this->kernel = $kernel;
	}


	/**
	 * @inheritDoc
	 */
	protected function execute (InputInterface $input, OutputInterface $output) : int
	{
		$io = new TorrStyle($input, $output);
		$io->title("Assets: Debug");

		$this->printNamespaces($io);
		$this->printFileTypes($io);

		return 0;
	}

	/**
	 */
	private function printNamespaces (TorrStyle $io) : void
	{
		$io->section("Namespaces");
		$rows = [];
		$projectDir = u($this->kernel->getProjectDir());

		$namespaces = $this->namespaceRegistry->getNamespaces();
		\usort($namespaces, "strnatcasecmp");

		foreach ($namespaces as $namespace)
		{
			$path = u($this->namespaceRegistry->getNamespacePath($namespace));

			if ($path->startsWith((string) $projectDir))
			{
				$path = $path
					->slice($projectDir->length())
					->prepend(".");
			}

			$rows[] = [
				"<fg=yellow>@{$namespace}</>",
				$path,
			];
		}

		if (empty($rows))
		{
			$io->warning("No namespaces registed");
			return;
		}

		$io->table(
			["Namespace", "Path"],
			$rows,
		);
	}


	/**
	 * Prints all file types
	 */
	private function printFileTypes (TorrStyle $io) : void
	{
		$io->section("File Types");
		$extensionMapping = $this->fileTypeRegistry->getExtensionMapping();
		\uksort($extensionMapping, "strnatcasecmp");
		$rows = [];

		foreach ($extensionMapping as $fileExtension => $fileType)
		{
			$rows[] = [
				"<fg=yellow>.{$fileExtension}</>",
				\get_class($fileType),
			];
		}

		$io->table(
			["Extension", "Path"],
			$rows,
		);
	}
}
