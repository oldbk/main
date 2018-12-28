<?php


namespace components\Enum;
use Carbon\Carbon;


/**
 * Class Events
 * @package components\Enum
 */
class Events
{
    public $current_month = 0;
    public $current_year = 0;
    public $events = [];

    public function __construct()
    {
        $this->current_month = Carbon::now()->month;
        $this->current_year = Carbon::now()->year;


        //Íîâîãîäíèå èâåíòû
        $this->setEvents([
            // ñ 1 äåêàáğÿ ïî 31 ÿíâàğÿ
            'sertstart' => $this->current_month == 12 ? mktime(0, 0, 0, 12, 1, $this->current_year) : mktime(0, 0, 0, 1, 1, $this->current_year),
            'sertend' => $this->current_month == 12 ? mktime(23, 59, 59, 12, 31, $this->current_year) : mktime(23, 59, 59, 1, 30, $this->current_year),

            /*
                [2:06:53] Òğèíèòè: îò 55600008
                äî 55600047
                [2:07:14] Òğèíèòè: íà áóäóùåå ñåáå çàïèøè. ïîäàğêè 1 äåêàáğÿ. Ğàçäåë - Çèìíèå ïîäàğêè
                âñå ïîäàğêè òîêà êğåäîâûå
            */


            /*
                ëàğöû ñ 10 äåêàáğÿ äî 31 äåêàáğÿ // îòêëş÷àåì àâòîñòàğò
            */
            'larcistart' => mktime(0, 0, 0, 12, 10, $this->current_year - 2),
            'larciend' => mktime(23, 59, 59, 12, 31, $this->current_year - 2),


            /* ¸ëêà íà öï ñ 15 äåêàáğÿ äî 30 ÿíâàğÿ 23:59  */
            'elkacpstart' => $this->current_month == 12 ? mktime(0, 0, 0, 12, 15, $this->current_year) : mktime(0, 0, 0, 1, 1, $this->current_year),
            'elkacpend' => $this->current_month == 12 ? mktime(23, 59, 59, 12, 31, $this->current_year) : mktime(23, 59, 59, 2, 29, $this->current_year),


            /* ïîäàğîê íà ¸ëêå ìîæíî âçÿòü ñ 20 äåêàáğÿ ñ 1:30 íî÷è ïî 29 ÿíâàğÿ 23:59  */
            'elkacpgiftstart' => $this->current_month == 12 ? mktime(1, 30, 0, 12, 20, $this->current_year) : mktime(0, 0, 0, 1, 1, $this->current_year),
            'elkacpgiftend' => $this->current_month == 12 ? mktime(23, 59, 59, 12, 31, $this->current_year) : mktime(23, 59, 59, 1, 29, $this->current_year),

            /* åäà íà ¸ëêå ìîæíî âçÿòü ñ 29 äåêàáğÿ ïî 2 ÿíâàğÿ 23:59  */
            'elkacpeatstart' => $this->current_month == 12 ? mktime(0, 0, 0, 12, 29, $this->current_year) : mktime(0, 0, 0, 1, 1, $this->current_year),
            'elkacpeatend' => $this->current_month == 12 ? mktime(23, 59, 59, 1, 2, $this->current_year + 1) : mktime(23, 59, 59, 1, 2, $this->current_year),

            /* îáğàç íà ¸ëêå ìîæíî âçÿòü ñ 20 äåêàáğÿ c 1:30 ïî 10 ÿíâàğÿ 23:59  */
            'elkacpcarnavalstart' => $this->current_month == 12 ? mktime(1, 30, 0, 12, 20, $this->current_year) : mktime(0, 0, 0, 1, 1, $this->current_year),
            'elkacpcarnavalend' => $this->current_month == 12 ? mktime(23, 59, 59, 12, 31, $this->current_year) : mktime(23, 59, 59, 1, 10, $this->current_year),

            /* ïğîäàæà ¸ëîê è âûïàäåíèå */
            'elkadropstart' => $this->current_month == 12 ? mktime(0, 0, 0, 12, 15, $this->current_year) : mktime(0, 0, 0, 1, 1, $this->current_year),
            'elkadropend' => $this->current_month == 12 ? mktime(23, 59, 59, 2, 28, $this->current_year + 1) : mktime(23, 59, 59, 2, 28, $this->current_year),

            /* 10% îïûòà çà ïîğàæåíèå ñ 29 äåêàáğÿ ïî 2 ÿíâàğÿ 23:59  */
            'ngloseexpstart' => $this->current_month == 12 ? mktime(0, 0, 0, 12, 29, $this->current_year) : mktime(0, 0, 0, 1, 1, $this->current_year),
            'ngloseexpend' => $this->current_month == 12 ? mktime(23, 59, 59, 12, 31, $this->current_year) : mktime(23, 59, 59, 1, 2, $this->current_year),

            /* 10% îïûòà çà ïîğàæåíèå ñ 00:00 14 ãî ïî 23:59 15 ÿíâàğÿ  */
            'hbloseexpstart' => mktime(0, 0, 0, 1, 14, $this->current_year),
            'hbloseexpend' => mktime(23, 59, 59, 1, 15, $this->current_year),

            /* ñêóïêà */
            'skupkastart' => mktime(0, 0, 0, 12, 29, $this->current_year),
            'skupkaend' => mktime(23, 59, 59, 12, 30, $this->current_year),

            /* íîãîäíÿÿ âîëíà õàóñà */
            'nghaosstart' => $this->current_month == 12 ? mktime(0, 0, 0, 12, 29, $this->current_year) : mktime(0, 0, 0, 1, 1, $this->current_year),
            'nghaosend' => $this->current_month == 12 ? mktime(23, 59, 59, 12, 31, $this->current_year) : mktime(23, 59, 59, 1, 2, $this->current_year),

            /* âîëíà íà ãîäîâùèíó ñ 00:00 14 ãî ïî 23:59 15 ÿíâàğÿ */
            'hbhaosstart' => mktime(0, 0, 0, 1, 14, $this->current_year),
            'hbhaosend' => mktime(23, 59, 59, 1, 15, $this->current_year),
        ]);

    }

    public function setEvents(array $events)
    {
        $this->events = $events;
    }


}