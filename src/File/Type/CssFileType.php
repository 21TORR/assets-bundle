<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\File\Data\FileProcessData;
use Torr\Assets\File\Type\Css\CssUrlRewriter;
use Torr\Assets\File\Type\Header\FileInfoCommentGenerator;
use Torr\HtmlBuilder\Node\HtmlElement;

final class CssFileType extends FileType
{
	private FileInfoCommentGenerator $infoComment;
	private CssUrlRewriter $urlRewriter;


	/**
	 * @inheritDoc
	 */
	public function __construct (CssUrlRewriter $urlRewriter)
	{
		$this->infoComment = new FileInfoCommentGenerator("/*", "*/");
		$this->urlRewriter = $urlRewriter;
	}


	/**
	 * @inheritDoc
	 */
	public function getSupportedExtensions () : array
	{
		return ["css"];
	}


	/**
	 * @inheritDoc
	 */
	public function processForDebug (FileProcessData $data) : string
	{
		return $this->infoComment->generateInfoComment($data->getAsset(), $data->getFilePath()) .
			"\n" .
			$this->urlRewriter->rewrite($data->getContent());
	}


	/**
	 * @inheritDoc
	 */
	public function processForProduction (FileProcessData $data) : string
	{
		return $this->urlRewriter->rewrite($data->getContent());
	}


	/**
	 * @inheritDoc
	 */
	public function canHaveAssetDependencies () : bool
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function createHtmlIncludeElement (string $path, array $parameter = []) : HtmlElement
	{
		return new HtmlElement("link", [
			"rel" => "stylesheet",
			"href" => $path,
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function isEmbeddable () : bool
	{
		return true;
	}
}
