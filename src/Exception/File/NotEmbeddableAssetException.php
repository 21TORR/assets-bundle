<?php declare(strict_types=1);

namespace Torr\Assets\Exception\File;

use Torr\Assets\Exception\AssetsException;

final class NotEmbeddableAssetException extends \InvalidArgumentException implements AssetsException
{
}
