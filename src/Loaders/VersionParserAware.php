<?php

namespace Certificationy\Loaders;

use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\VersionParser;

trait VersionParserAware
{
    /**
     * @var VersionParser|null
     */
    private $versionParser;

    protected function getVersionParser() : VersionParser
    {
        if (!$this->versionParser instanceof VersionParser) {
            $this->versionParser = new VersionParser();
        }

        return $this->versionParser;
    }

    protected function versionApplies(
        array $givenLibraryVersions = null,
        array $configuredLibraryVersions = null
    ) : bool {
        // default behavior: always return true, if configuration or question don't provide versions
        if (null === $configuredLibraryVersions || null === $givenLibraryVersions) {
            return true;
        }

        $numberOfLibraries = count($givenLibraryVersions);
        $matches = 0;

        foreach ($givenLibraryVersions as $libraryName => $libraryVersion) {
            if (!isset($configuredLibraryVersions[$libraryName])) {
                break;
            }

            /** @var ConstraintInterface $questionVersionConstraint */
            $configuredVersionConstraint = $this->getVersionParser()->parseConstraints($configuredLibraryVersions[$libraryName]);

            /** @var ConstraintInterface $givenLibraryVersionConstraint */
            $givenLibraryVersionConstraint = $this->getVersionParser()->parseConstraints($libraryVersion);

            $matches += $givenLibraryVersionConstraint->matches($configuredVersionConstraint) ? 1 : 0;
        }

        return $matches === $numberOfLibraries;
    }
}
