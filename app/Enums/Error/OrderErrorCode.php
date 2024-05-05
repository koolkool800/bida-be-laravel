<?php declare(strict_types=1);

namespace App\Enums\Error;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class OrderErrorCode extends Enum
{
    const ORDER_NOT_FOUND = 'ODER_001'; 
    const ORDER_ALREADY_CHECK_OUT = 'ODER_003'; 
}
