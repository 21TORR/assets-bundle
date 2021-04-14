<?php declare(strict_types=1);

namespace Torr\Assets\File\Type;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Torr\Assets\Asset\AssetInterface;
use Torr\Assets\Asset\StoredAsset;
use Torr\Assets\File\Data\FileProcessData;
use Torr\Assets\File\Type\Css\CssUrlRewriter;
use Torr\Assets\File\Type\Header\FileInfoCommentGenerator;
use Torr\HtmlBuilder\Node\HtmlAttributes;
use Torr\HtmlBuilder\Node\HtmlElement;

final class CssFileType extends FileType implements ProcessableFileTypeInterface, ServiceSubscriberInterface
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
		/** @var CssUrlRewriter $urlRewriter */
		$urlRewriter = $this->locator->get(CssUrlRewriter::class);

		return $this->infoComment->generateInfoComment($data->getAsset(), $data->getFilePath()) .
			"\n" .
			$urlRewriter->rewrite($data->getStorageMap(), $data->getContent());
	}


	/**
	 * @inheritDoc
	 */
	public function processForProduction (FileProcessData $data) : string
	{
		/** @var CssUrlRewriter $urlRewriter */
		$urlRewriter = $this->locator->get(CssUrlRewriter::class);

		return $urlRewriter->rewrite($data->getStorageMap(), $data->getContent());
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
	public function shouldHashFileName () : bool
	{
		// the file names will already be hashed by your build tool, hopefully
		return false;
	}


	/**
	 * @inheritDoc
	 */
	public function createHtmlIncludeElement (string $url, AssetInterface $asset, array $attributes = []) : HtmlElement
	{
		$attrs = new HtmlAttributes($attributes);
		$attrs->set("rel", "stylesheet");
		$attrs->set("href", $url);

		if ($asset instanceof StoredAsset)
		{
			$attrs->set("integrity", "{$asset->getHashAlgorithm()}-{$asset->getHash()}");
		}

		return new HtmlElement("link", $attrs);
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
