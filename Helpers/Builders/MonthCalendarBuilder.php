<?php

namespace GDCalendar\Helpers\Builders;

class MonthCalendarBuilder extends CalendarBuilder
{

    public function getFirstDay()
    {
        $firstDay = $this->getYear() . '-' . $this->getMonth() . '-01';

        $dateComponents = $this->getDateComponents();

        $dayOfWeek = $dateComponents['wday'];

        $lastDateComponents = $this->getLastDateComponents();

        $lastDayOfMonth = $lastDateComponents['mday'];

        $lastMonth = str_pad($lastDateComponents['mon'], 2, "0", STR_PAD_LEFT);

        if ($dayOfWeek > 0) {
        	$day = str_pad($lastDayOfMonth - $dayOfWeek + 1, 2, "0", STR_PAD_LEFT);
            $firstDay = ($this->getMonth() !== 1 ? $this->getYear() : ($this->getYear() - 1) ) . '-' . $lastMonth . '-' . $day;
        }
        return $firstDay;
    }

    public function getLastDay()
    {
        $lastDay = $this->getYear() . '-' . $this->getMonth() . '-' . $this->getDaysCount();

        $dateComponents = $this->getLastDayDateComponents();

        $dayOfWeek = $dateComponents['wday'];

        $nextMonth = $this->getNextDateComponents();

        $nextMonth = str_pad($nextMonth['mon'], 2, "0", STR_PAD_LEFT);

        if($dayOfWeek != 6){
	        $day = str_pad(6 - $dayOfWeek, 2, "0", STR_PAD_LEFT);
            $lastDay = ($this->getMonth() !== 12 ? $this->getYear() : ($this->getYear() + 1) ) . '-' . $nextMonth . '-' . $day;
        }

        return $lastDay;
    }

}