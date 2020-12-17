<?php declare(strict_types=1);

namespace Torr\Assets\Exception\Asset;

use Torr\Assets\Exception\AssetsException;

final class InvalidAssetException extends \InvalidArgumentException implements AssetsException
{
}
