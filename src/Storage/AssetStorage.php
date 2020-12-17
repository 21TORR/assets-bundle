<?php declare(strict_types=1);

namespace Torr\Assets\Storage;

final class AssetStorage
{
	private string $outputDir;

	/**
	 */
	public function __construct (string $outputDir)
	{
		$this->outputDir = \trim($outputDir, "/");
	}

	/**
	 * @internal
	 */
	public function getOutputDir () : string
	{
		return $this->outputDir;
	}
}
