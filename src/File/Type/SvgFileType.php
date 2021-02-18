<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\File\Data\FileProcessData;
use Torr\Assets\File\Type\Header\FileInfoCommentGenerator;
use Torr\HtmlBuilder\Node\HtmlElement;

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
	public function processForDebug (FileProcessData $data) : string
	{
		// the comment must be at the bottom, as otherwise the SVG would become invalid
		return parent::processForDebug($data) .
			"\n" .
			$this->infoComment->generateInfoComment($data->getAsset(), $data->getFilePath());
	}

	/**
	 * @inheritDoc
	 */
	public function isEmbeddable () : bool
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function createHtmlIncludeElement (string $path, array $parameter = []) : HtmlElement
	{
		return new HtmlElement("img", [
			"src" => $path,
			"alt" => "",
		]);
	}
}
