<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\File\Data\FileProcessData;
use Torr\Assets\File\Type\Css\CssUrlRewriter;
use Torr\Assets\File\Type\Header\FileInfoCommentGenerator;

final class CssFileType extends FileType
{
	private FileInfoCommentGenerator $infoComment;

	/** @required */
	public CssUrlRewriter $urlRewriter;


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
	public function getEmbedCode (string $path, array $parameter = []) : string
	{
		return "<link rel=\"stylesheet\" href=\"{$path}\" />";
	}
}
