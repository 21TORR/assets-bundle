<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Torr\Assets\File\Data\FileProcessData;
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
	public function getEmbedCode (string $path, array $parameter = []) : string
	{
		if ($parameter['modern'])
		{
			return "<script type=\"module\" src=\"{$path}\" ></script>";
		}

		if ($parameter['legacy'])
		{
			return "<script nomodule=\"true\" src=\"{$path}\" ></script>";
		}

		return "<script src=\"{$path}\" ></script>";
	}
}
