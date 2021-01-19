<?php declare(strict_types=1);

namespace Torr\Assets\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mime\MimeTypesInterface;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Exception\Asset\InvalidAssetException;
use Torr\Assets\Exception\File\FileNotFoundException;
use Torr\Assets\File\FileLoader;
use Torr\Assets\Storage\AssetMap;
use Torr\Rad\Controller\BaseController;

final class EmbedController extends BaseController
{
	public function embed (
	    FileLoader $fileLoader,
	    KernelInterface $kernel,
	    MimeTypesInterface $mimeTypes,
	    string $namespace,
        string $path
    )
	{
        try
        {
            $asset = new Asset($namespace, $path);
            $mode = $kernel->isDebug() ? FileLoader::MODE_DEBUG : FileLoader::MODE_PRODUCTION;

            return new Response(
                $fileLoader->loadFile(new AssetMap(), $asset, $mode),
                200,
                [
                    "Content-Type" => $mimeTypes->getMimeTypes($asset->getExtension())[0] ?? "application/octet-stream",
                ]
            );
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