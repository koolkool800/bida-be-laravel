<?php declare(strict_types=1);

namespace App\Enums\Error;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class SettingTableErrorCode extends Enum
{
    const SETTING_TABLE_ALREADY_EXIST = 'SETTING_TABLE_001'; 
    const SETTING_TABLE_NOT_FOUND = 'SETTING_TABLE_002';
}
