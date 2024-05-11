<?php declare(strict_types=1);

namespace App\Enums\Error;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ProductErrorCode extends Enum
{
    const PRODUCT_NOT_FOUND = 'PRODUCT_001'; 
    const PRODUCT_ALREADY_EXIST = 'PRODUCT_003'; 
}
