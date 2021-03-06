<?php declare(strict_types=1);

namespace Torr\Assets\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mime\MimeTypesInterface;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\Asset\InvalidAssetException;
use Torr\Assets\Exception\File\FileNotFoundException;
use Torr\Assets\File\FileLoader;
use Torr\Assets\File\FileTypeRegistry;
use Torr\Assets\File\Type\ProcessableFileTypeInterface;
use Torr\Assets\Manager\AssetsManager;
use Torr\Assets\Namespaces\NamespaceRegistry;
use Torr\Rad\Controller\BaseController;

final class EmbedController extends BaseController
{
	public function embed (
		FileLoader $fileLoader,
		KernelInterface $kernel,
		FileTypeRegistry $fileTypeRegistry,
		NamespaceRegistry $namespaceRegistry,
		MimeTypesInterface $mimeTypes,
		AssetsManager $assetsManager,
		string $namespace,
		string $path
	) : Response
	{
		try
		{
			$asset = new Asset($namespace, $path);
			$fileType = $fileTypeRegistry->getFileType($asset);

			if ($fileType instanceof ProcessableFileTypeInterface)
			{
				$assetMap = $assetsManager->getStorageMap();

				$response = new Response(
					$kernel->isDebug()
						? $fileLoader->loadForDebug($assetMap, $asset)
						: $fileLoader->loadForProduction($assetMap, $asset),
				);

			}
			else
			{
				$response = new BinaryFileResponse($namespaceRegistry->getAssetFilePath($asset));
			}

			$response->headers->set(
				"Content-Type",
				$mimeTypes->getMimeTypes((string) $asset->getExtension())[0] ?? "application/octet-stream"
			);

			return $response;
		}
		catch (FileNotFoundException | InvalidAssetException $exception)
		{
			throw $this->createNotFoundException(
				"Asset not found: @{$namespace}/{$path}",
				$exception
			);
		}
	}
}
