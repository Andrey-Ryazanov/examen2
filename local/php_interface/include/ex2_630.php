<?php

use Bitrix\Main\EventManager;
use CUserFieldEnum;

class SearchEventHandler
{
    private const REVIEWS_IBLOCK_ID = 6;

    public static function init(): void
    {
        EventManager::getInstance()->addEventHandler(
            "search", 
            "BeforeIndex", 
            [self::class, 'BeforeIndexHandler']
        );
    }

    public static function BeforeIndexHandler($arFields): array
    {
        if (
            $arFields["MODULE_ID"] === "iblock"
            && (int)$arFields["PARAM2"] === self::REVIEWS_IBLOCK_ID
        ) 
        {
            $res = CIBlockElement::GetProperty(
                self::REVIEWS_IBLOCK_ID,
                $arFields['ITEM_ID'],
                [],
                ['CODE' => 'AUTHOR']
            );

            if ($prop = $res->Fetch()) 
            {
                if (!empty($prop['VALUE'])) 
                {
                    $user = CUser::GetByID($prop['VALUE'])->Fetch();
                    if (!empty($user['UF_USER_CLASS']) && array_key_exists("TITLE", $arFields)) {

                        $enumRes = CUserFieldEnum::GetList([], [
                            "ID" => $user['UF_USER_CLASS']
                        ]);
                        
                        if ($enum = $enumRes->Fetch()) 
                        {
                            $arFields['TITLE'] .= ' Класс: ' . $enum['VALUE'];
                        } 
                        else 
                        {
                            $arFields['TITLE'] .= ' Класс (не найден): ' . $user['UF_USER_CLASS'];
                        }
                    }
                }
            }
        }

        return $arFields;
    }
}

SearchEventHandler::init();
