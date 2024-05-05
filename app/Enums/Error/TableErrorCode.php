<?php declare(strict_types=1);

namespace App\Enums\Error;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TableErrorCode extends Enum
{
    const TABLE_ALREADY_EXIST = 'TABLE_001'; 
    const TABLE_NOT_FOUND = 'TABLE_002';
    const TABLE_NOT_AVAILABLE = 'TABLE_003';
    const TABLE_AVAILABLE = 'TABLE_004';
}
