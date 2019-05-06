<?php
declare(strict_types=1);

namespace Clickstorm\CsWebp\Controller;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheGroupException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClearImagesController
{
    /**
     * This method is called by the CacheMenuItem in the Backend
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws NoSuchCacheGroupException
     */
    public static function clearImages(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $repository = GeneralUtility::makeInstance(ProcessedFileRepository::class);
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);

        // remove all processed files
        $repository->removeAll();

       $command = sprintf('rm -rf %sfileadmin/_processed_/*', Environment::getPublicPath() . '/');
       CommandUtility::exec($command);

        // clear page caches
        $cacheManager->flushCachesInGroup('pages');
        $response->getBody()->write('');
        return $response;

    }
}