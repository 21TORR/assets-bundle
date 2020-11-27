<?php declare(strict_types=1);

namespace Torr\Assets\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use function Symfony\Component\String\u;
use Torr\Assets\Namespaces\NamespaceRegistry;
use Torr\Rad\Command\TorrCliStyle;

final class DebugNamespacesCommand extends Command
{
	protected static $defaultName = "21torr:assets:debug";
	private NamespaceRegistry $namespaceRegistry;
	private KernelInterface $kernel;

	/**
	 * @inheritDoc
	 */
	public function __construct (
		NamespaceRegistry $namespaceRegistry,
		KernelInterface $kernel
	)
	{
		parent::__construct();
		$this->namespaceRegistry = $namespaceRegistry;
		$this->kernel = $kernel;
	}


	/**
	 * @inheritDoc
	 */
	protected function execute (InputInterface $input, OutputInterface $output) : int
	{
		$io = new TorrCliStyle($input, $output);
		$io->title("Debug: Assets");

		$this->printNamespaces($io);

		return 0;
	}

	private function printNamespaces (TorrCliStyle $io) : void
	{
		$io->section("Namespaces");
		$rows = [];
		$projectDir = u($this->kernel->getProjectDir());

		foreach ($this->namespaceRegistry as $namespace => $rawPath)
		{
			$path = u($rawPath);

			if ($path->startsWith($projectDir))
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
			$rows
		);
	}
}
