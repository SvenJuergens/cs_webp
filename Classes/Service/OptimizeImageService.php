<?php
namespace Clickstorm\CsWebp\Service;

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
	 * @param string $extension
	 */
	public function process($file, $extension = NULL) {
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
            CommandUtility::exec($command, $output, $returnValue);
            if ((bool)$this->configuration['debug'] === TRUE && is_object($GLOBALS['BE_USER'])) {
                $GLOBALS['BE_USER']->writelog($command . ' exited with ' . $returnValue . '. Output was: ' . implode(' ', $output), 'cs_webp', 0);
            }
        }
	}
}