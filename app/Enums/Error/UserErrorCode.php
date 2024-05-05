<?php declare(strict_types=1);

namespace App\Enums\Error;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class UserErrorCode extends Enum
{
    const USER_ALREADY_EXIST = 'USER_001'; 
    const USER_NOT_FOUND = 'USER_002';
}
