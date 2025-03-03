<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Julio Borges <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\Extension;

use function count;
use function explode;
use function implode;
use function is_file;
use function strpos;
use PharIo\Manifest\ApplicationName;
use PharIo\Manifest\Exception as ManifestException;
use PharIo\Manifest\ManifestLoader;
use PharIo\Version\Version as PharIoVersion;
use PHPUnit\Runner\Version;
use SebastianBergmann\FileIterator\Facade as FileIteratorFacade;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class PharLoader
{
    /**
     * @psalm-return array{loadedExtensions: list<string>, notLoadedExtensions: list<string>}
     */
    public function loadPharExtensionsInDirectory(string $directory): array
    {
        $loadedExtensions    = [];
        $notLoadedExtensions = [];

        foreach ((new FileIteratorFacade)->getFilesAsArray($directory, '.phar') as $file) {
            if (!is_file('phar://' . $file . '/manifest.xml')) {
                $notLoadedExtensions[] = $file . ' is not an extension for PHPUnit';

                continue;
            }

            try {
                $applicationName = new ApplicationName('phpunit/phpunit');
                $version         = new PharIoVersion($this->phpunitVersion());
                $manifest        = ManifestLoader::fromFile('phar://' . $file . '/manifest.xml');

                if (!$manifest->isExtensionFor($applicationName)) {
                    $notLoadedExtensions[] = $file . ' is not an extension for PHPUnit';

                    continue;
                }

                if (!$manifest->isExtensionFor($applicationName, $version)) {
                    $notLoadedExtensions[] = $file . ' is not compatible with this version of PHPUnit';

                    continue;
                }
            } catch (ManifestException $e) {
                $notLoadedExtensions[] = $file . ': ' . $e->getMessage();

                continue;
            }

            /**
             * @noinspection PhpIncludeInspection
             *
             * @psalm-suppress UnresolvableInclude
             */
            require $file;

            $loadedExtensions[] = $manifest->getName()->asString() . ' ' . $manifest->getVersion()->getVersionString();
        }

        return [
            'loadedExtensions'    => $loadedExtensions,
            'notLoadedExtensions' => $notLoadedExtensions,
        ];
    }

    private function phpunitVersion(): string
    {
        $version = Version::id();

        if (strpos($version, '-') === false) {
            return $version;
        }

        $parts = explode('.', explode('-', $version)[0]);

        if (count($parts) === 2) {
            $parts[] = 0;
        }

        return implode('.', $parts);
    }
}
