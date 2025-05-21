<?
use Bitrix\Main\EventManager;
use Bitrix\Main\Mail\Event;

class ChangeUserClassEventHandler
{
    public static function init(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->addEventHandler(
            'main',
            'onBeforeUserUpdate',
            [
                self::class,
                'onBeforeUserUpdateHandler'
            ]
        );
    }

    public static function onBeforeUserUpdateHandler(&$arFields): void
    {
        self::onChangeUserClass($arFields);
    }

    private static function onChangeUserClass(&$arFields): void
    {
        if (empty($arFields['ID'])) {
            return;
        }
    
        $user = \CUser::GetByID($arFields['ID'])->Fetch();
    
        if (!$user) {
            return;
        }
    
        $oldUserClass = $user["UF_USER_CLASS"] ?? null;
        $newUserClass = $arFields["UF_USER_CLASS"] ?? null;
    
        if (self::isUserClassChanged($oldUserClass, $newUserClass))
        {
            $cfields = [
                "OLD_USER_CLASS" => $oldUserClass,
                "NEW_USER_CLASS" => $newUserClass
            ];

            self::eventSender(
                "EX2_AUTHOR_INFO", 
                "s1", 
                44,           
                $cfields
            );
        }
    }
    
    public static function isUserClassChanged($old, $new): bool
    {
        return $old !== $new;
    }

    private static function eventSender($eventName="", $lid="s1", $messageId="", $cfields=[]): void
    {
        Event::send([                
            "EVENT_NAME" => $eventName,
            "LID" => $lid,
            "MESSAGE_ID" => $messageId,
            "C_FIELDS" => $cfields
        ]);
    }
    
}

ChangeUserClassEventHandler::init();