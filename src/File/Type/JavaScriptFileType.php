<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\File\Data\FileProcessData;
use Torr\Assets\File\Type\Header\FileInfoCommentGenerator;
use Torr\HtmlBuilder\Node\HtmlElement;

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
	public function processForDebug (FileProcessData $data) : string
	{
		return $this->infoComment->generateInfoComment($data->getAsset(), $data->getFilePath()) .
			"\n" .
			parent::processForDebug($data);
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
	public function createHtmlIncludeElement (string $path, array $parameter = []) : HtmlElement
	{
		$element = new HtmlElement("script", [
			"src" => $path,
		]);

		if (isset($parameter['modern']))
		{
			$element->getAttributes()->set("type", "module");
		}
		else if (isset($parameter['legacy']))
		{
			$element->getAttributes()->set("nomodule", true);
		}

		return $element;
	}


	/**
	 * @inheritDoc
	 */
	public function isEmbeddable () : bool
	{
		return true;
	}
}
