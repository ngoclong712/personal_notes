<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class SubtaskStatusEnum extends Enum
{
    public const IN_PROGRESS = 1;
    public const COMPLETED = 2;
    public const CANCELLED = 3;
    public const OVERDUE = 4;
}
