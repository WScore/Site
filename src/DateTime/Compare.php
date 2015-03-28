<?php
namespace WScore\Site\DateTime;

class Compare
{
    /**
     * @var Date
     */
    private $date;

    /**
     * 
     */
    public function __construct()
    {
    }

    /**
     * @param Date $date
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
     * @param  Date $dt
     *
     * @return boolean
     */
    public function eq(Date $dt)
    {
        return $this->date == $dt;
    }

    /**
     * Determines if the instance is not equal to another
     *
     * @param  Date $dt
     *
     * @return boolean
     */
    public function ne(Date $dt)
    {
        return !$this->eq($dt);
    }

    /**
     * Determines if the instance is greater (after) than another
     *
     * @param  Date $dt
     *
     * @return boolean
     */
    public function gt(Date $dt)
    {
        return $this->date > $dt;
    }

    /**
     * Determines if the instance is greater (after) than or equal to another
     *
     * @param  Date $dt
     *
     * @return boolean
     */
    public function gte(Date $dt)
    {
        return $this->date >= $dt;
    }

    /**
     * Determines if the instance is less (before) than another
     *
     * @param  Date $dt
     *
     * @return boolean
     */
    public function lt(Date $dt)
    {
        return $this->date < $dt;
    }

    /**
     * Determines if the instance is less (before) or equal to another
     *
     * @param  Date $dt
     *
     * @return boolean
     */
    public function lte(Date $dt)
    {
        return $this->date <= $dt;
    }

    /**
     * Determines if the instance is between two others
     *
     * @param  Date    $dt1
     * @param  Date    $dt2
     * @param  boolean $equal Indicates if a > and < comparison should be used or <= or >=
     *
     * @return boolean
     */
    public function between(Date $dt1, Date $dt2, $equal = true)
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
     * @param  Date $dt
     *
     * @return Date
     */
    public function min(Date $dt)
    {
        return $this->lt($dt) ? $this->date : $dt;
    }

    /**
     * Get the maximum instance between a given instance (default now) and the current instance.
     *
     * @param  Date $dt
     *
     * @return Date
     */
    public function max(Date $dt)
    {
        return $this->gt($dt) ? $this->date : $dt;
    }
}