<?php

declare(strict_types=1);

namespace SvenJuergens\CsWebp\Service;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

class OptimizeImageService
{
    public function __construct(
        protected array $extensionConfiguration
    ) {}

    public function process(string $file, ?string $extension = null): void
    {
        if (!file_exists($file)) {
            return;
        }
        if ($extension === null) {
            $pathInfo = pathinfo($file);
            if (isset($pathInfo['extension'])) {
                $extension = $pathInfo['extension'];
            }
        }
        $extension = strtolower((string)$extension);

        if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            return;
        }
        if (!str_contains($file, 'fileadmin/_processed_')) {
            return;
        }

        $webpFile = str_replace('.' . $extension, '.webp', $file);
        $quality = MathUtility::forceIntegerInRange((int)($this->extensionConfiguration['quality'] ?? 75), 1, 100);
        $useCwebp = (bool)($this->extensionConfiguration['useCwebp'] ?? false);

        if ($useCwebp) {
            $command = sprintf(
                '%s -q %s %s -o %s',
                escapeshellcmd((string)($this->extensionConfiguration['cwebpPath'] ?? 'cwebp')),
                escapeshellarg((string)$quality),
                escapeshellarg($file),
                escapeshellarg($webpFile)
            );
        } else {
            $command = sprintf(
                'convert %s -quality %s %s',
                escapeshellarg($file),
                escapeshellarg((string)$quality),
                escapeshellarg($webpFile)
            );
        }

        $output = [];
        $returnValue = 0;
        CommandUtility::exec($command, $output, $returnValue);

        if ((bool)($this->extensionConfiguration['debug'] ?? false) && $this->getBackendUser() !== null) {
            $this->getBackendUser()->writelog(
                4,
                0,
                0,
                0,
                '[cs_webp] ' . htmlspecialchars($command) . ' exited with ' . htmlspecialchars((string)$returnValue) . '. Output was: ' . htmlspecialchars(implode(' ', $output)),
                []
            );
        }
        GeneralUtility::fixPermissions($file);
    }

    protected function getBackendUser(): ?BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'] ?? null;
    }
}
