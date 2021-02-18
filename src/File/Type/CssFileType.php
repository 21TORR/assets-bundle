<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Torr\Assets\File\Data\FileProcessData;
use Torr\Assets\File\Type\Css\CssUrlRewriter;
use Torr\Assets\File\Type\Header\FileInfoCommentGenerator;
use Torr\HtmlBuilder\Node\HtmlElement;

final class CssFileType extends FileType implements ServiceSubscriberInterface
{
	private FileInfoCommentGenerator $infoComment;
	private ContainerInterface $locator;


	/**
	 * @inheritDoc
	 */
	public function __construct (ContainerInterface $locator)
	{
		$this->infoComment = new FileInfoCommentGenerator("/*", "*/");
		$this->locator = $locator;
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
		$urlRewriter = $this->locator->get(CssUrlRewriter::class);

		return $this->infoComment->generateInfoComment($data->getAsset(), $data->getFilePath()) .
			"\n" .
			$urlRewriter->rewrite($data->getContent());
	}


	/**
	 * @inheritDoc
	 */
	public function processForProduction (FileProcessData $data) : string
	{
		$urlRewriter = $this->locator->get(CssUrlRewriter::class);

		return $urlRewriter->rewrite($data->getContent());
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

	/**
	 * @inheritDoc
	 */
	public static function getSubscribedServices ()
	{
		return [
			CssUrlRewriter::class,
		];
	}
}
