<?php
namespace SvenJuergens\CsWebp;

use TYPO3\CMS\Core\Resource\Service\FileProcessingService;
use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use SvenJuergens\CsWebp\Service\OptimizeImageService;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileAspects {

	/**
	 * @var OptimizeImageService
	 */
	protected $service;

	public function __construct() {
		$this->service = GeneralUtility::makeInstance(OptimizeImageService::class);
	}

	/**
  * Called when a file was processed
  *
  * @param FileProcessingService $fileProcessingService
  * @param DriverInterface $driver
  * @param ProcessedFile $processedFile
  */
 public function processFile($fileProcessingService, $driver, $processedFile) {
		if ($processedFile->isUpdated() === TRUE) {
			// ToDo: Find better possibility for getPublicUrl()
			$this->service->process(Environment::getPublicPath() . '/' . $processedFile->getPublicUrl(), $processedFile->getExtension());
		}
	}
}
