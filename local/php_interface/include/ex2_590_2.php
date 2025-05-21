<?php
use Bitrix\Main\EventManager;

class ReviewsAuthorEventHandler
{
    public static function init()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler(
            'iblock', 
            'OnBeforeIBlockElementUpdate', 
            [self::class, 'onBeforeElementUpdateHandler']
        );
    }
    
    public static function onBeforeElementUpdateHandler(&$arFields)
    {
        self::authorChecker($arFields);
    }
    
    private static function authorChecker(&$arFields)
    {
        if ((int)$arFields["IBLOCK_ID"] !== 6) {
            return;
        }
        
        $elementId = $arFields['ID'];
        $propValues = $arFields['PROPERTY_VALUES'][10] ?? [];
        $newAuthorId = !empty($propValues) ? reset($propValues)['VALUE'] : null;

        $property = \CIBlockElement::GetProperty(
            $arFields['IBLOCK_ID'],
            $elementId,
            [],
            ['CODE' => 'AUTHOR']
        )->GetNext();
        
        $oldAuthorId = $property['VALUE'];

        if ($newAuthorId !== $oldAuthorId)
        {
            $description = sprintf(
                'В рецензии [%s] изменился автор с [%s] на [%s]',
                $elementId,
                $oldAuthorId,
                $newAuthorId
            );
            self::logger('INFO', 'ex2_590', 'iblock', $elementId, $description);
        }
    }

    private static function logger($severity, $auditTypeId, $moduleId, $itemId, $description)
    {
        \CEventLog::Add([
            'SEVERITY' => $severity,
            'AUDIT_TYPE_ID' => $auditTypeId,
            'MODULE_ID' => $moduleId,
            'ITEM_ID' => $itemId,
            'DESCRIPTION' => $description,
        ]);
    }
}   

ReviewsAuthorEventHandler::init();