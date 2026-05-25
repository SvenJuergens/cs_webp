<?php

declare(strict_types=1);

namespace SvenJuergens\CsWebp\EventListener;

use SvenJuergens\CsWebp\Service\OptimizeImageService;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent;

#[AsEventListener(
    identifier: 'cs-webp/convert-processed-file',
)]
final readonly class AfterFileProcessing
{
    public function __construct(
        private OptimizeImageService $optimizeImageService,
    ) {}

    public function __invoke(AfterFileProcessingEvent $event): void
    {
        $processedFile = $event->getProcessedFile();
        if (!$processedFile->isUpdated()) {
            return;
        }
        $this->optimizeImageService->process(
            Environment::getPublicPath() . '/' . ltrim((string)$processedFile->getPublicUrl(), '/'),
            $processedFile->getExtension()
        );
    }
}
