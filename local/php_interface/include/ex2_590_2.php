<?php
IncludeModuleLangFile(__FILE__);
use Bitrix\Main\EventManager;

class ReviewsAuthorEventHandler
{
    private const AUTHOR_PROPERTY_ID = 10;

    public static function init(): void
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler(
            'iblock', 
            'OnBeforeIBlockElementUpdate', 
            [self::class, 'onBeforeElementUpdateHandler']
        );
    }
    
    public static function onBeforeElementUpdateHandler(&$arFields): void
    {
        self::authorChecker($arFields);
    }
    
    private static function authorChecker(&$arFields): void
    {
        if ((int)$arFields["IBLOCK_ID"] !== 6) {
            return;
        }
        
        $elementId = $arFields['ID'];
        $newAuthorId = self::extractPropertyValue($arFields['PROPERTY_VALUES'][self::AUTHOR_PROPERTY_ID] ?? []);

        $property = \CIBlockElement::GetProperty(
            $arFields['IBLOCK_ID'],
            $elementId,
            [],
            ['CODE' => 'AUTHOR']
        )->GetNext();
        
        $oldAuthorId = $property['VALUE'] ?? "";

        if (self::isChangedAuthor($newAuthorId, $oldAuthorId))
        {
            $description = GetMessage(
                "TEXT_CHANGE_AUTHOR", 
                [
                    "#ELEMENT_ID#" => $elementId,
                    "#OLD_AUTHOR_ID#" => self::formatAuthorValue($oldAuthorId),
                    "#NEW_AUTHOR_ID#" => self::formatAuthorValue($newAuthorId)
                ]
            );
            
            self::logger('INFO', 'ex2_590', 'iblock', $elementId, $description);
        }
    }

    private static function formatAuthorValue(string $authorId): string
    {
        return $authorId !== "" ? $authorId : "нет";
    }

    private static function isChangedAuthor(string $newAuthorId, string $oldAuthorId): bool
    {
        return $newAuthorId !== $oldAuthorId;
    }

    private static function extractPropertyValue(array $propertyArray): string
    {
        foreach ($propertyArray as $key => $item) {
            if (is_array($item) && array_key_exists('VALUE', $item)) {
                return $item['VALUE'];
            }
        }

        return "";
    }

    private static function logger($severity, $auditTypeId, $moduleId, $itemId, $description): void
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