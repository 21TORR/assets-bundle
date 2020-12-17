<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\Asset\Asset;
use Torr\Assets\File\Type\Header\FileInfoCommentGenerator;

final class JavaScriptFileType extends FileType
{
	private FileInfoCommentGenerator $infoComment;

	/**
	 * @inheritDoc
	 */
	public function __construct ()
	{
		$this->infoComment = new FileInfoCommentGenerator("/*", "*/");
	}


	/**
	 * @inheritDoc
	 */
	public function getSupportedExtensions () : array
	{
		return ["js"];
	}

	/**
	 * @inheritDoc
	 */
	public function processForDebug (Asset $asset, string $content, string $filePath) : string
	{
		return $this->infoComment->generateInfoComment($asset, $filePath) .
			"\n" .
			parent::processForDebug($asset, $content, $filePath);
	}
}
