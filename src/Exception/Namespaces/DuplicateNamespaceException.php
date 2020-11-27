<?php declare(strict_types=1);

namespace Torr\Assets\Exception\Namespaces;

use Torr\Assets\Exception\AssetsException;

final class DuplicateNamespaceException extends \OverflowException implements AssetsException
{
}
