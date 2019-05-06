<?php
defined('TYPO3_MODE') or die();

(function () {

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'ext-cswebp-clear-processed-images',
        \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        ['source' => 'EXT:cs_webp/Resources/Public/Images/clear_cache_icon.png']
    );

    // The Backend-MenuItem in ClearCache-Pulldown
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['additionalBackendItems']['cacheActions']['tx_cswebp'] =
        \Clickstorm\CsWebp\Toolbar\ToolbarItem::class;
})();