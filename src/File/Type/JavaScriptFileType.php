<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\File\Data\FileProcessData;
use Torr\Assets\File\Type\Header\FileInfoCommentGenerator;
use Torr\HtmlBuilder\Node\HtmlAttributes;
use Torr\HtmlBuilder\Node\HtmlElement;

final class JavaScriptFileType extends FileType implements ProcessableFileTypeInterface
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
	public function processForDebug (FileProcessData $data) : string
	{
		return $this->infoComment->generateInfoComment($data->getAsset(), $data->getFilePath()) .
			"\n" .
			$data->getContent();
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
	public function shouldHashFileName () : bool
	{
		// the file names will already be hashed by your build tool, hopefully
		return false;
	}


	/**
	 * @inheritDoc
	 */
	public function createHtmlIncludeElement (string $url, array $attributes = []) : HtmlElement
	{
		$attrs = new HtmlAttributes($attributes);
		$attrs->set("defer", true);
		$attrs->set("src", $url);

		return new HtmlElement("script", $attrs);
	}
}
