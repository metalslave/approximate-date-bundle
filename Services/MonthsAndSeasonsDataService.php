<?php

namespace Metalslave\ApproximateDateBundle\Services;

/**
 * Class MonthsAndSeasonsDataService.
 */
class MonthsAndSeasonsDataService implements MonthAndSeasonsInterface
{
    private $monthsAndSeasons =
        [
            'uk' =>
                [
                    'січня' => ['січень', 'зима'],
                    'лютого' => ['лютий', 'зима'],
                    'березня' => ['березень', 'весна'],
                    'квітня' => ['квітень', 'весна'],
                    'травня' => ['травень', 'весна'],
                    'червня' => ['червень', 'літо'],
                    'липня' => ['липень', 'літо'],
                    'серпня' => ['серпень', 'літо'],
                    'вересня' => ['вересень', 'осінь'],
                    'жовтня' => ['жовтень', 'осінь'],
                    'листопада' => ['листопад', 'осінь'],
                    'грудня' => ['грудень', 'зима'],
                ],
            'en' =>
                [
                    'January' => ['January', 'Winter'],
                    'February' => ['February', 'Winter'],
                    'March' => ['March', 'Spring'],
                    'April' => ['April', 'Spring'],
                    'May' => ['May', 'Spring'],
                    'June' => ['June', 'Summer'],
                    'July' => ['July', 'Summer'],
                    'August' => ['August', 'Summer'],
                    'September' => ['September', 'Autumn'],
                    'October' => ['October', 'Autumn'],
                    'November' => ['November', 'Autumn'],
                    'December' => ['December', 'Winter'],
                ],
        ];

    /**
     * @return array
     */
    public function getMonthsAndSeasons()
    {
        return $this->monthsAndSeasons;
    }
}
