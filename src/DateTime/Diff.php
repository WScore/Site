<?php
namespace WScore\Site\DateTime;

/**
 * Class Diff
 *
 * inspired by (i.e. some code are from) Carbon.
 * https://github.com/briannesbitt/Carbon

 * @package WScore\Site\DateTime
 */
class Diff
{
    /**
     * Number of X in Y
     */
    const MONTHS_PER_YEAR    = 12;
    const WEEKS_PER_YEAR     = 52;
    const DAYS_PER_WEEK      = 7;
    const HOURS_PER_DAY      = 24;
    const MINUTES_PER_HOUR   = 60;
    const SECONDS_PER_MINUTE = 60;

    /**
     * @var static   immutable object
     */
    private static $self;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * private, cannot construct other than getInstance method.
     */
    private function __construct()
    {
    }

    /**
     * @param DateTime $date
     * @return Diff
     */
    public static function start($date)
    {
        if (!static::$self) {
            static::$self = new static;
        }
        $new       = clone(static::$self);
        $new->date = $date;
        return $new;
    }

    /**
     * Get the difference in years
     *
     * @param  DateTime  $dt
     * @param  boolean $abs Get the absolute of the difference
     *
     * @return int
     */
    public function inYears(DateTime $dt = null, $abs = true)
    {
        $dt = $dt ?: DateTime::now();

        return intval($this->date->diff($dt, $abs)->format('%r%y'));
    }

    /**
     * Get the difference in months
     *
     * @param  DateTime  $dt
     * @param  boolean $abs Get the absolute of the difference
     *
     * @return integer
     */
    public function inMonths(DateTime $dt = null, $abs = true)
    {
        $dt = $dt ?: DateTime::now();

        return $this->date->diff->inYears($dt, $abs) * self::MONTHS_PER_YEAR
        + (int)$this->date->diff($dt, $abs)->format('%r%m');
    }

    /**
     * Get the difference in days
     *
     * @param  DateTime  $dt
     * @param  boolean $abs Get the absolute of the difference
     *
     * @return integer
     */
    public function inDays(DateTime $dt = null, $abs = true)
    {
        $dt = $dt ?: DateTime::now();

        return intval($this->date->diff($dt, $abs)->format('%r%a'));
    }

    /**
     * Get the difference in hours
     *
     * @param  DateTime  $dt
     * @param  boolean $abs Get the absolute of the difference
     *
     * @return integer
     */
    public function inHours(DateTime $dt = null, $abs = true)
    {
        $dt = $dt ?: DateTime::now();

        return intval($this->date->diff->inMinutes($dt, $abs) / self::MINUTES_PER_HOUR);
    }

    /**
     * Get the difference in minutes
     *
     * @param  DateTime  $dt
     * @param  boolean $abs Get the absolute of the difference
     *
     * @return integer
     */
    public function inMinutes(DateTime $dt = null, $abs = true)
    {
        $dt = $dt ?: DateTime::now();

        return intval($this->date->diff->inSeconds($dt, $abs) / self::SECONDS_PER_MINUTE);
    }

    /**
     * Get the difference in seconds
     *
     * @param  DateTime  $dt
     * @param  boolean $abs Get the absolute of the difference
     *
     * @return integer
     */
    public function inSeconds(DateTime $dt = null, $abs = true)
    {
        $dt = $dt ?: DateTime::now();

        $value = $dt->getTimestamp() - $this->date->getTimestamp();

        return $abs ? abs($value) : $value;
    }

}