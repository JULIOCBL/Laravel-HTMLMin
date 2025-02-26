<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Julio Borges <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class OriginalConstructorInvocationRequiredException extends \PHPUnit\Framework\Exception implements Exception
{
    public function __construct()
    {
        parent::__construct('Proxying to original methods requires invoking the original constructor');
    }
}
