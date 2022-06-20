<?php declare(strict_types=1);

namespace Torr\Assets\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Mime\MimeTypesInterface;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\AssetsException;
use Torr\Assets\File\FileLoader;
use Torr\Assets\File\FileTypeRegistry;
use Torr\Assets\File\Type\ProcessableFileTypeInterface;
use Torr\Assets\Manager\AssetsManager;
use Torr\Assets\Namespaces\NamespaceRegistry;

final class EmbedController extends AbstractController
{
	public function embed (
		FileLoader $fileLoader,
		KernelInterface $kernel,
		FileTypeRegistry $fileTypeRegistry,
		NamespaceRegistry $namespaceRegistry,
		MimeTypesInterface $mimeTypes,
		AssetsManager $assetsManager,
		?Profiler $profiler,
		string $namespace,
		string $path,
	) : Response
	{
		// disable profiler
		$profiler?->disable();

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
				$mimeTypes->getMimeTypes((string) $asset->getExtension())[0] ?? "application/octet-stream",
			);

			return $response;
		}
		catch (AssetsException $exception)
		{
			throw $this->createNotFoundException(
				"Asset not found: @{$namespace}/{$path}",
				$exception,
			);
		}
	}
}
