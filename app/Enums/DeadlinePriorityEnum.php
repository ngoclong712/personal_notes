<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class DeadlinePriorityEnum extends Enum
{
    public const LOW = 1;
    public const MEDIUM = 2;
    public const HIGH = 3;
}
