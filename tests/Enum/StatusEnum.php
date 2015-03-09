<?php
namespace tests\Enum;

use WScore\Site\Enum\AbstractEnum;

/**
 * Class StatusEnum
 *
 * @package tests\Enum
 *
 * @method static StatusEnum ACTIVE()
 * @method static StatusEnum CANCEL()
 * @method static StatusEnum MAY_BE()
 *
 * @method bool isActive()
 * @method bool isCancel()
 * @method bool isMaybe()
 */
class StatusEnum extends AbstractEnum
{
    const __DEFAULT = self::ACTIVE;
    const ACTIVE = 'A';
    const MAY_BE = '?';
    const CANCEL = 'X';

    protected static $choices = [
        self::ACTIVE => 'active',
        self::MAY_BE => 'may be',
        self::CANCEL => 'cancel'
    ];

    protected $yesNo = [
        self::ACTIVE => 'active',
        self::CANCEL => 'cancel'
    ];

    public function withYesNo()
    {
        return $this->cloneWithSelection($this->yesNo);
    }
}

