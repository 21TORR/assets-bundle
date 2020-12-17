<?php declare(strict_types=1);

namespace Torr\Assets\File\Type\Header;

use Torr\Assets\Asset\Asset;

final class FileInfoCommentGenerator
{
	private string $openingComment;
	private string $closingComment;

	/**
	 */
	public function __construct (string $openingComment, string $closingComment)
	{
		$this->openingComment = $openingComment;
		$this->closingComment = $closingComment;
	}

	/**
	 * Generates the header comment
	 */
	public function generateInfoComment (Asset $asset, string $filePath) : string
	{
		return <<<HEADER
{$this->openingComment}
    Embed asset
        {$asset->toAssetPath()}
    from file 
        {$filePath}
{$this->closingComment}
HEADER;
	}
}
