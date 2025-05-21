<?php
IncludeModuleLangFile(__FILE__);
use Bitrix\Main\EventManager;

class ReviewsEventHandler
{
    public static function init(): void
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler(
            'iblock', 
            'OnBeforeIBlockElementAdd', 
            [self::class, 'onBeforeElementAddHandler']
        );
        $eventManager->addEventHandler(
            'iblock', 
            'OnBeforeIBlockElementUpdate', 
            [self::class, 'onBeforeElementUpdateHandler']
        );
    }
    public static function onBeforeElementAddHandler(&$arFields): bool
    {
        return self::previewTextChecker($arFields);
    }
    
    public static function onBeforeElementUpdateHandler(&$arFields): bool
    {
        return self::previewTextChecker($arFields);
    }
    
    private static function previewTextChecker(&$arFields): bool
    {
        if ((int)$arFields["IBLOCK_ID"] !== 6) {
            return true;
        }
    
        $previewText = trim($arFields["PREVIEW_TEXT"]);
        $minSize = 5;
    
        if (mb_strlen($previewText, 'UTF-8') < $minSize) {
            global $APPLICATION;
            $message = GetMessage(
                "PREVIEW_TEXT_ERROR_MESSAGE", 
                ['#LEN#' => mb_strlen($previewText, 'UTF-8')]
            );
            $APPLICATION->ThrowException(
                $message
            );
            return false;
        }
    
        if (strpos($previewText, '#del#') !== false) {
            $arFields['PREVIEW_TEXT'] = str_replace('#del#', '', $previewText);
        }
    
        return true;
    }
}   

ReviewsEventHandler::init();