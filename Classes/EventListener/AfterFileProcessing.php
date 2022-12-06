<?php

namespace SvenJuergens\CsWebp\EventListener;

use SvenJuergens\CsWebp\Service\OptimizeImageService;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AfterFileProcessing {

    public function __invoke(AfterFileProcessingEvent $event): void
    {
        if ($event->getProcessedFile()->isUpdated() === TRUE) {
            $service = GeneralUtility::makeInstance(OptimizeImageService::class);
            $service->process(
                Environment::getPublicPath() . '/' . ltrim($event->getProcessedFile()->getPublicUrl(), '/'),
                $event->getProcessedFile()->getExtension()
            );
        }
    }
}
