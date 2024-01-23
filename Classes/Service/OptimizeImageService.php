<?php

namespace SvenJuergens\CsWebp\Service;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

class OptimizeImageService
{
    /**
     * Initialize
     * @param array $extensionConfiguration
     */
    public function __construct(
        protected array $extensionConfiguration
    ) {}

    /**
     * Perform image optimization
     *
     * @param string $file
     * @param string|null $extension
     * @return false|void
     */
    public function process(string $file, string $extension = null)
    {
        if (!file_exists($file)) {
            return false;
        }
        if ($extension === null) {
            $pathInfo = pathinfo($file);
            if ($pathInfo['extension'] !== null) {
                $extension = $pathInfo['extension'];
            }
        }
        $extension = strtolower($extension);

        if (($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png') && str_contains($file, 'fileadmin/_processed_')) {
            $webpFile = str_replace('.' . $extension, '.webp', $file);
            $quality = MathUtility::forceIntegerInRange($this->extensionConfiguration['quality'], 1, 100);
            $command = sprintf('convert %s -quality %s %s', $file, $quality, $webpFile);
            if (isset($this->extensionConfiguration['useCwebp']) && (bool)$this->extensionConfiguration['useCwebp'] === true) {
                $command = sprintf('%s -q %s %s -o %s', $this->extensionConfiguration['cwebpPath'], $quality, $file, $webpFile);
            }
        }

        if (isset($command)) {
            $output = [];
            $returnValue = 0;
            CommandUtility::exec(escapeshellcmd($command), $output, $returnValue);
            if ((bool)$this->extensionConfiguration['debug'] === true && is_object($this->getBackendUser())) {
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
    }

    /**
     * Returns the current BE user.
     *
     * @return BackendUserAuthentication|null
     */
    protected function getBackendUser(): ?BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'] ?? null;
    }
}
