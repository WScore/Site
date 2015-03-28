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
 * @property-read integer $date Y-m-d (Year-month-date)
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
 * @property-read string $ymd  Ymd (Year-month-date)
 * @property-read string $iso  Y-m-d (Year-month-date in ISO like format)
 * @property-read string $W3C for HTML5 datetime input element
 * @property-read Compare $is for comparing date type
 * @property-read Diff $diff for calculating difference
 */
class DateTime extends \DateTimeImmutable
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
        '平成' => '1989-01-08',
        '昭和' => '1926-12-25',
        '大正' => '1912-07-30',
        '明治' => '1868-01-25',
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
        'ymd'         => 'Ymd',
        'iso'         => 'Y-m-d',
        'W3C'         => \DateTime::W3C,
    ];

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param string             $time
     * @param \DateTimeZone|null $timezone
     */
    public function __construct($time = "now", $timezone = NULL)
    {
        parent::__construct($time, $timezone);
    }

    /**
     * @param null|bool|string $time
     * @return DateTime
     */
    public static function now($time=null)
    {
        if($time === false) {
            // reset $now.
            static::$now = null;

        } elseif($time === true) {
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

    /**
     * @param int $y
     * @param int $m
     * @param int $d
     * @param int $hour
     * @param int $min
     * @param int $sec
     * @return static
     */
    public static function createDate($y, $m=1, $d=1, $hour=0, $min=0, $sec=0)
    {
        return new static("$y-$m-$d $hour:$min:$sec");
    }

    /**
     * @param int  $y
     * @param int  $m
     * @param bool $endOfDay
     * @return static
     */
    public static function createEndOfMonth($y, $m, $endOfDay=false)
    {
        $dt = new static("$y-$m-01");
        if($endOfDay) {
            return new static($dt->format('Y-m-t 23:59:59'));
        }
        return new static($dt->format('Y-m-t 00:00:00'));
    }

    /**
     * @param int $h
     * @param int $m
     * @param int $s
     * @return static
     */
    public static function createTime($h=0, $m=0, $s=0)
    {
        return new static("0-0-0 $h:$m:$s");
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
            if (in_array($name, ['W3C', 'ymd', 'iso'])) {
                return (string) $this->format($this->properties[$name]);
            }
            return (int)$this->format($this->properties[$name]);
        }
        switch($name) {
            case 'is':
                return Compare::start($this);
            case 'diff':
                return Diff::start($this);
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
     * @param string $sep
     * @return string
     */
    public function toDate($sep='/')
    {
        return $this->format(implode($sep,['Y','m','d']));
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
            return $w[0];
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
                if($year === 1) {
                    return '元';
                }
                return $year;
            }
        }
        return $year;
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

    /**
     * @param string $string
     * @return static
     */
    public function modify($string)
    {
        return parent::modify($string);
    }
}