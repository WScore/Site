<?php
namespace WScore\Site\DateTime;

class Compare
{
    /**
     * @var DateTime
     */
    private $date;

    /**
     * 
     */
    public function __construct()
    {
    }

    /**
     * @param DateTime $date
     * @return Compare
     */
    public function start($date)
    {
        $new = clone($this);
        $new->date = $date;
        return $new;
    }
    
    /**
     * Determines if the instance is equal to another
     *
     * @param  DateTime $dt
     *
     * @return boolean
     */
    public function eq(DateTime $dt)
    {
        return $this->date == $dt;
    }

    /**
     * Determins if the instance is the same date (ignoring time part).
     *
     * @param DateTime $dt
     * @return bool
     */
    public function sameDate(DateTime $dt)
    {
        return $this->date->ymd === $dt->ymd;
    }

    /**
     * Determines if the instance is not equal to another
     *
     * @param  DateTime $dt
     *
     * @return boolean
     */
    public function ne(DateTime $dt)
    {
        return !$this->eq($dt);
    }

    /**
     * Determines if the instance is greater (after) than another
     *
     * @param  DateTime $dt
     *
     * @return boolean
     */
    public function gt(DateTime $dt)
    {
        return $this->date > $dt;
    }

    /**
     * Determines if the instance is greater (after) than or equal to another
     *
     * @param  DateTime $dt
     *
     * @return boolean
     */
    public function gte(DateTime $dt)
    {
        return $this->date >= $dt;
    }

    /**
     * Determines if the instance is less (before) than another
     *
     * @param  DateTime $dt
     *
     * @return boolean
     */
    public function lt(DateTime $dt)
    {
        return $this->date < $dt;
    }

    /**
     * Determines if the instance is less (before) or equal to another
     *
     * @param  DateTime $dt
     *
     * @return boolean
     */
    public function lte(DateTime $dt)
    {
        return $this->date <= $dt;
    }

    /**
     * Determines if the instance is between two others
     *
     * @param  DateTime    $dt1
     * @param  DateTime    $dt2
     * @param  boolean $equal Indicates if a > and < comparison should be used or <= or >=
     *
     * @return boolean
     */
    public function between(DateTime $dt1, DateTime $dt2, $equal = true)
    {
        if ($dt1->is->gt($dt2)) {
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
     * @param  DateTime $dt
     *
     * @return DateTime
     */
    public function min(DateTime $dt)
    {
        return $this->lt($dt) ? $this->date : $dt;
    }

    /**
     * Get the maximum instance between a given instance (default now) and the current instance.
     *
     * @param  DateTime $dt
     *
     * @return DateTime
     */
    public function max(DateTime $dt)
    {
        return $this->gt($dt) ? $this->date : $dt;
    }
}