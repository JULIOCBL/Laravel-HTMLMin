<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Julio Borges <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Rule;

use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class AnyParameters implements ParametersRule
{
    public function toString(): string
    {
        return 'with any parameters';
    }

    public function apply(BaseInvocation $invocation): void
    {
    }

    public function verify(): void
    {
    }
}
