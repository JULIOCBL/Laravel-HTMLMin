<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Julio Borges <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint;

use const DIRECTORY_SEPARATOR;
use function explode;
use function implode;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function strtr;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class StringMatchesFormatDescription extends RegularExpression
{
    /**
     * @var string
     */
    private $string;

    public function __construct(string $string)
    {
        parent::__construct(
            $this->createPatternFromFormat(
                $this->convertNewlines($string),
            ),
        );

        $this->string = $string;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other value or object to evaluate
     */
    protected function matches($other): bool
    {
        return parent::matches(
            $this->convertNewlines($other),
        );
    }

    protected function failureDescription($other): string
    {
        return 'string matches format description';
    }

    protected function additionalFailureDescription($other): string
    {
        $from = explode("\n", $this->string);
        $to   = explode("\n", $this->convertNewlines($other));

        foreach ($from as $index => $line) {
            if (isset($to[$index]) && $line !== $to[$index]) {
                $line = $this->createPatternFromFormat($line);

                if (preg_match($line, $to[$index]) > 0) {
                    $from[$index] = $to[$index];
                }
            }
        }

        $this->string = implode("\n", $from);
        $other        = implode("\n", $to);

        return (new Differ(new UnifiedDiffOutputBuilder("--- Expected\n+++ Actual\n")))->diff($this->string, $other);
    }

    private function createPatternFromFormat(string $string): string
    {
        $string = strtr(
            preg_quote($string, '/'),
            [
                '%%' => '%',
                '%e' => '\\' . DIRECTORY_SEPARATOR,
                '%s' => '[^\r\n]+',
                '%S' => '[^\r\n]*',
                '%a' => '.+',
                '%A' => '.*',
                '%w' => '\s*',
                '%i' => '[+-]?\d+',
                '%d' => '\d+',
                '%x' => '[0-9a-fA-F]+',
                '%f' => '[+-]?\.?\d+\.?\d*(?:[Ee][+-]?\d+)?',
                '%c' => '.',
            ],
        );

        return '/^' . $string . '$/s';
    }

    private function convertNewlines(string $text): string
    {
        return preg_replace('/\r\n/', "\n", $text);
    }
}
