<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\Asset\AssetInterface;
use Torr\Assets\File\Data\FileProcessData;
use Torr\Assets\File\Type\Header\FileInfoCommentGenerator;
use Torr\HtmlBuilder\Node\HtmlAttributes;
use Torr\HtmlBuilder\Node\HtmlElement;

final class SvgFileType extends FileType implements ProcessableFileTypeInterface
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
		return $data->getContent() .
			"\n" .
			$this->infoComment->generateInfoComment($data->getAsset(), $data->getFilePath());
	}

	/**
	 * @inheritDoc
	 */
	public function processForProduction(FileProcessData $data) : string
	{
		return $data->getContent();
	}

	/**
	 * @inheritDoc
	 */
	public function createHtmlIncludeElement (string $url, AssetInterface $asset, array $attributes = []) : HtmlElement
	{
		$attrs = new HtmlAttributes($attributes);
		$attrs->set("alt", "");
		$attrs->set("src", $url);

		return new HtmlElement("img", $attrs);
	}
}
