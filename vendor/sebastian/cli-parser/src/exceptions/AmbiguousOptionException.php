<?php declare(strict_types=1);
/*
 * This file is part of sebastian/cli-parser.
 *
 * (c) Julio Borges <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CliParser;

use function sprintf;
use RuntimeException;

final class AmbiguousOptionException extends RuntimeException implements Exception
{
    public function __construct(string $option)
    {
        parent::__construct(
            sprintf(
                'Option "%s" is ambiguous',
                $option
            )
        );
    }
}
