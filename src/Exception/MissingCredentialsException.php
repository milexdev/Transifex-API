<?php declare(strict_types=1);

namespace Milex\Transifex\Exception;

/**
 * Exception defining missing API credentials.
 */
final class MissingCredentialsException extends \RuntimeException implements TransifexException
{
}
