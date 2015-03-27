<?php
namespace WScore\Site\DateTime;

/**
 * Class Date
 *
 * immutable DateTime class with Japanese stuff.
 *
 * inspired by (i.e. some code are from) Carbon.
 * https://github.com/briannesbitt/Carbon
 *
 * @package WScore\Site\DateTime
 *
 * @property-read integer $year
 * @property-read integer $month
 * @property-read integer $day
 * @property-read integer $hour
 * @property-read integer $minute
 * @property-read integer $second
 * @property-read integer $timestamp seconds since the Unix Epoch
 * @property-read integer $dayOfWeek 0 (for Sunday) through 6 (for Saturday)
 * @property-read integer $dayOfYear 0 through 365
 * @property-read integer $weekOfYear ISO-8601 week number of year, weeks starting on Monday
 * @property-read integer $daysInMonth number of days in the given month
 *
 */
class Date extends \DateTimeImmutable
{
    /**
     * @var string    default format
     */
    public static $format = 'Y-m-d H:i:s';

    /**
     * @var self
     */
    private static $now;

    private $weeks = ['日', '月', '火', '水', '木', '金', '土'];

    private $nenGou = [
        '明治' => '1868-01-25',
        '大正' => '1912-07-30',
        '昭和' => '1926-12-25',
        '平成' => '1989-01-08',
    ];

    private $properties = [
        'year'        => 'Y',
        'yearIso'     => 'o',
        'month'       => 'n',
        'day'         => 'j',
        'hour'        => 'G',
        'minute'      => 'i',
        'second'      => 's',
        'micro'       => 'u',
        'dayOfWeek'   => 'w',
        'dayOfYear'   => 'z',
        'weekOfYear'  => 'W',
        'daysInMonth' => 't',
        'timestamp'   => 'U',
    ];

    /**
     * @param null|bool|string $time
     * @return Date
     */
    public static function now($time=null)
    {
        static::$now;
        if($time === true) {
            // regenerate now.
            static::$now = new static;

        } elseif(is_string($time)) {
            // use new now with $time string.
            static::$now = new static($time);

        } elseif(!static::$now) {
            // first time. create new.
            static::$now = new static;
        }
        return static::$now;
    }
    // +----------------------------------------------------------------------+
    //  getting properties and output
    // +----------------------------------------------------------------------+
    /**
     * Get a property
     *
     * @param  string $name
     * @throws \InvalidArgumentException
     * @return integer
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->properties)) {
            return (int)$this->format($this->properties[$name]);
        }
        throw new \InvalidArgumentException;
    }

    /**
     * Format the instance as a string using the set format
     *
     * @return string
     */
    public function __toString()
    {
        return $this->format(static::$format);
    }

    /**
     * Format the instance as date
     *
     * @return string
     */
    public function toDay()
    {
        return $this->format('Y-m-d');
    }

    /**
     * 日本の暦に対応。
     * %w -> 短い曜日
     * %W -> 長い曜日
     * %G -> 元号
     * %Y -> 元号での年
     *
     * @param string $format
     * @return string
     */
    public function format($format)
    {
        $format = preg_replace_callback('/%([a-zA-Z]{1})/', function ($w) {
            switch ($w[1]) {
                case 'w': // 短い曜日
                    return $this->jaWeek();
                case 'W': // 長い曜日
                    return $this->jaWeek(true);
                case 'Y': // 元号での年
                    return $this->jaYear();
                case 'G': // 元号
                    return $this->jaGenGou();
            }
            return '';
        }, $format);
        return parent::format($format);
    }

    /**
     * 日本の年号を返す。明治以前の場合は空白を返す。
     *
     * @return string
     */
    public function jaGenGou()
    {
        $date = $this->format('Y-m-d');
        foreach ($this->nenGou as $gou => $start) {
            if ($date >= $start) {
                return $gou;
            }
        }
        return '';
    }

    /**
     * 日本の年号を返す。明治以前の場合は空白を返す。
     *
     * @return string
     */
    public function jaYear()
    {
        $date = $this->format('Y-m-d');
        $year = (int)$this->format('Y');
        foreach ($this->nenGou as $gou => $start) {
            if ($date >= $start) {
                $year -= (int)(substr($start, 0, 4) - 1);
                return $year;
            }
        }
        return '';
    }

    /**
     * 日本の曜日（短い）を返す。
     *
     * @param bool $long
     * @return string
     */
    public function jaWeek($long = false)
    {
        $week = isset($this->weeks[$this->format('w')]) ? $this->weeks[$this->format('w')] : null;
        if ($long) {
            $week .= '曜日';
        }
        return $week;
    }

    // +----------------------------------------------------------------------+
    //  date comparison
    //  these codes are from Carbon.
    // +----------------------------------------------------------------------+
    /**
     * Determines if the instance is equal to another
     *
     * @param  self $dt
     *
     * @return boolean
     */
    public function eq(self $dt)
    {
        return $this == $dt;
    }

    /**
     * Determines if the instance is not equal to another
     *
     * @param  self $dt
     *
     * @return boolean
     */
    public function ne(self $dt)
    {
        return !$this->eq($dt);
    }

    /**
     * Determines if the instance is greater (after) than another
     *
     * @param  self $dt
     *
     * @return boolean
     */
    public function gt(self $dt)
    {
        return $this > $dt;
    }

    /**
     * Determines if the instance is greater (after) than or equal to another
     *
     * @param  self $dt
     *
     * @return boolean
     */
    public function gte(self $dt)
    {
        return $this >= $dt;
    }

    /**
     * Determines if the instance is less (before) than another
     *
     * @param  self $dt
     *
     * @return boolean
     */
    public function lt(self $dt)
    {
        return $this < $dt;
    }

    /**
     * Determines if the instance is less (before) or equal to another
     *
     * @param  self $dt
     *
     * @return boolean
     */
    public function lte(self $dt)
    {
        return $this <= $dt;
    }

    /**
     * Determines if the instance is between two others
     *
     * @param  self    $dt1
     * @param  self    $dt2
     * @param  boolean $equal Indicates if a > and < comparison should be used or <= or >=
     *
     * @return boolean
     */
    public function between(self $dt1, self $dt2, $equal = true)
    {
        if ($dt1->gt($dt2)) {
            $temp = $dt1;
            $dt1  = $dt2;
            $dt2  = $temp;
        }

        if ($equal) {
            return $this->gte($dt1) && $this->lte($dt2);
        } else {
            return $this->gt($dt1) && $this->lt($dt2);
        }
    }

    /**
     * Get the minimum instance between a given instance (default now) and the current instance.
     *
     * @param  self $dt
     *
     * @return static
     */
    public function min(self $dt)
    {
        return $this->lt($dt) ? $this : $dt;
    }

    /**
     * Get the maximum instance between a given instance (default now) and the current instance.
     *
     * @param  self $dt
     *
     * @return static
     */
    public function max(self $dt)
    {
        return $this->gt($dt) ? $this : $dt;
    }

}