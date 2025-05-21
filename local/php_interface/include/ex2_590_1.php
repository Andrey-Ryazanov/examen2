<?php
use Bitrix\Main\EventManager;

class ReviewsEventHandler
{
    public static function init()
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
    public static function onBeforeElementAddHandler(&$arFields)
    {
        return self::previewTextChecker($arFields);
    }
    
    public static function onBeforeElementUpdateHandler(&$arFields)
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
            $APPLICATION->ThrowException(
                'Текст анонса слишком короткий: ' . mb_strlen($previewText, 'UTF-8')
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