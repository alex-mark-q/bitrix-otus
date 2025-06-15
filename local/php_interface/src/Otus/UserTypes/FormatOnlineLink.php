<?php

namespace Otus\UserTypes;

use Bitrix\Main\UserField\Types\StringFormattedType;
use CUserTypeManager;

class FormatOnlineLink extends StringFormattedType
{
    public const
        USER_TYPE_ID = 'telegram_string_formatted_link',
        RENDER_COMPONENT = 'otus:field.linkaction'; // компонент который обрабатывает ссылку на телеграм

    // public const RENDER_COMPONENT = 'otus:field.linkaction';

    public static function getDescription(): array {
        return [
            'DESCRIPTION' => 'Онлайн ссылка',
            'BASE_TYPE' => CUserTypeManager::BASE_TYPE_STRING,
        ];
    }

    public static function getDbColumnType(): string
    {
        return 'text';
    }

}