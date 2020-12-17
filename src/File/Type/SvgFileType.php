<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\Asset\Asset;
use Torr\Assets\File\Type\Header\FileInfoCommentGenerator;

final class SvgFileType extends FileType
{
	private FileInfoCommentGenerator $infoComment;

	/**
	 * @inheritDoc
	 */
	public function __construct ()
	{
		$this->infoComment = new FileInfoCommentGenerator("<!--", "-->");
	}


	/**
	 * @inheritDoc
	 */
	public function getSupportedExtensions () : array
	{
		return ["svg"];
	}

	/**
	 * @inheritDoc
	 */
	public function processForDebug (Asset $asset, string $content, string $filePath) : string
	{
		// the comment must be at the bottom, as otherwise the SVG would become invalid
		return parent::processForDebug($asset, $content, $filePath) .
			"\n" .
			$this->infoComment->generateInfoComment($asset, $filePath);
	}
}
