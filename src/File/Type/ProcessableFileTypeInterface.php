<?php
declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\File\Data\FileProcessData;

interface ProcessableFileTypeInterface
{
	/**
	 * Processes the file for usage in production
	 */
	public function processForProduction (FileProcessData $data) : string;


	/**
	 * Processes the file for usage in debug
	 */
	public function processForDebug (FileProcessData $data) : string;
}
