<?php
IncludeModuleLangFile(__FILE__);
use Bitrix\Main\Type\DateTime;

class Agent
{
    private const REVIEWS_IBLOCK_ID = 6;

    public static function Agent_ex_610(): string
    {
        CModule::IncludeModule('iblock');

        global $DB;
        $agentName = 'Agent::Agent_ex_610();';
        $query = "SELECT LAST_EXEC FROM b_agent WHERE NAME = '" . $DB->ForSql($agentName) . "'";
        $rsAgent = $DB->Query($query);

        $lastExec = (new DateTime())->add('-1 day');
        if ($arAgent = $rsAgent->Fetch()) {
            if (!empty($arAgent['LAST_EXEC'])) {
                $lastExec = new DateTime($arAgent['LAST_EXEC'], 'Y-m-d H:i:s');
            }
        }

        $filter = [
            'IBLOCK_ID' => self::REVIEWS_IBLOCK_ID,
            '>TIMESTAMP_X' => $lastExec,
        ];

        $count = 0;
        $countRes = \CIBlockElement::GetList([], $filter, ["ID"]);
        while ($countRes->Fetch()) {
            $count++;
        }

        $description = GetMessage(
            "AGENT_INFO_MESSAGE",
            [
                '#DATE#'  => $lastExec->toString(),
                '#COUNT#' => $count
            ]
        );

        self::log($description);
        return "Agent::Agent_ex_610();";
    }

    private static function log(string $message): void
    {
        \CEventLog::Add([
            "SEVERITY"      => "INFO",
            "AUDIT_TYPE_ID" => "ex2_610",
            "DESCRIPTION"   => $message,
        ]);
    }
}
