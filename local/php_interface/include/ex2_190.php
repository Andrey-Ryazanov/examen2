<?php
use Bitrix\Main\EventManager;

class AdminMenuEventHandler
{
    public static function init()
    {
        EventManager::getInstance()->addEventHandler(
            "main",
            "OnBuildGlobalMenu",
            [self::class, "onBuildGlobalMenu"],
            false,
            200
        );
    }
    
    private static function filterGlobalMenu(&$menu, $allowedItems)
    {
        foreach ($menu as $key => $item) {
            if (!in_array($key, $allowedItems)) {
                unset($menu[$key]);
            }
        }
    }

    public static function filterModuleMenu(&$moduleMenu, $allowedItems)
    {
        foreach ($moduleMenu as $key => $item)
        {
            if (!in_array($item['parent_menu'], $allowedItems))
            {
                unset($moduleMenu[$key]);
            }
        }
    }

    public static function onBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
    {        
        if (CSite::InGroup([5]))
        {
            // Оставить только раздел "Контент"
            self::filterGlobalMenu($aGlobalMenu, ["global_menu_content"]);
            self::filterModuleMenu($aModuleMenu, ["global_menu_content"]);
    
            // Добавим новый пункт "Быстрый доступ"
            $aGlobalMenu["global_menu_fast_access"] = [
                "menu_id" => "fast_access",
                "text" => "Быстрый доступ",
                "title" => "Быстрый доступ",
                "sort" => 150,
                "items_id" => "global_menu_fast_access_items",
                "items" => [
                    [
                        "text" => "Ссылка 1",
                        "url" => "https://test1",
                    ],
                    [
                        "text" => "Ссылка 2",
                        "url" => "https://test2",
                    ],
                ]
            ];
        }
    }    
}

AdminMenuEventHandler::init();