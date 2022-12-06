<?php
namespace SvenJuergens\CsWebp\Service;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

class OptimizeImageService {
    public $configuration;

    /**
     * Initialize
     */
    public function __construct() {
        $this->configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('cs_webp');
    }

    /**
     * Perform image optimization
     *
     * @param string $file
     * @param string|null $extension
     * @return false|void
     */
	public function process(string $file, string $extension = NULL) {
        if (!file_exists($file)) {
            return false;
        }

		if ($extension === NULL) {
			$pathInfo = pathinfo($file);
			if ($pathInfo['extension'] !== NULL) {
				$extension = $pathInfo['extension'];
			}
		}
		$extension = strtolower($extension);

		if (($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png') && strpos($file, 'fileadmin/_processed_') !== false) {
            $webpFile = str_replace("." . $extension, ".webp", $file);
            $quality = MathUtility::forceIntegerInRange($this->configuration['quality'],1,100);
            $command = sprintf('convert %s -quality %s -define webp:lossless=true %s', $file, $quality, $webpFile);
            if(isset($this->configuration['useCwebp']) && (bool)$this->configuration['useCwebp'] === true){
                $command = sprintf('%s -q %s %s -o %s', $this->configuration['cwebpPath'], $quality, $file, $webpFile);
            }
		}

        if (isset($command)) {
            $output = [];
            $returnValue = 0;
            CommandUtility::exec(escapeshellcmd($command), $output, $returnValue);
            if ((bool)$this->configuration['debug'] === TRUE && is_object($this->getBackendUser())) {
                $this->getBackendUser()->writelog(
                    4,
                    0,
                    0,
                    0,
                    '[cs_webp] '. htmlspecialchars($command) . ' exited with ' . htmlspecialchars((string)$returnValue) . '. Output was: ' . htmlspecialchars(implode(' ', $output)),
                    []
                );
            }
            GeneralUtility::fixPermissions($file);
        }
	}
    /**
     * Returns the current BE user.
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): ?BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'] ?? null;
    }
}
