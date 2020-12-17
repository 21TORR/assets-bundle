<?php declare(strict_types=1);

namespace Torr\Assets\Exception\File\Type;

use Torr\Assets\Exception\AssetsException;

final class DuplicateFileTypeExtensionException extends \InvalidArgumentException implements AssetsException
{
}
