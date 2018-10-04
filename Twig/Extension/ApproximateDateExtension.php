<?php

namespace Metalslave\ApproximateDateBundle\Twig\Extension;

use Metalslave\ApproximateDateBundle\Services\MonthAndSeasonsInterface;
use Sonata\IntlBundle\Twig\Extension\DateTimeExtension;

/**
 * Class ApproximateDateExtension for replace months name to nominative or season.
 */
class ApproximateDateExtension extends \Twig_Extension
{
    /** @var array  */
    private $monthsAndSeasonsData;

    /** @var DateTimeExtension */
    private $intlTwigDateTimeService;

    /** @var int */
    private $monthsAndSeasonsArrayIndex;

    const YEAR_SEASON_FORMAT = 'S';
    /**
     * AppDateTimeExtension constructor.
     *
     * @param DateTimeExtension        $intlTwigDateTimeService
     * @param MonthAndSeasonsInterface $monthsAndSeasonsDataService
     */
    public function __construct($intlTwigDateTimeService, MonthAndSeasonsInterface $monthsAndSeasonsDataService)
    {
        $this->intlTwigDateTimeService = $intlTwigDateTimeService;
        $this->monthsAndSeasonsData = $monthsAndSeasonsDataService->getMonthsAndSeasons();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('met_format_date', [$this, 'formatDate'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('met_format_date_day_month', [$this, 'formatDateDayMonth'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('met_format_date_only', [$this, 'formatDateOnly'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('met_format_time', [$this, 'formatTime'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('met_format_time_only', [$this, 'formatTimeOnly'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('met_format_datetime', [$this, 'formatDatetime'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param \Datetime|string|int $date
     * @param string|null          $pattern
     * @param string|null          $locale
     * @param string|null          $timezone
     * @param string|null          $dateType
     *
     * @return string
     */
    public function formatDate($date, $pattern = null, $locale = null, $timezone = null, $dateType = null)
    {
        if ($pattern) {
            $this->checkConvertToSeason($pattern);
        }

        $formattedDate = $this->intlTwigDateTimeService->formatDate($date, $pattern, $locale, $timezone, $dateType);
        if (null !== $pattern) {
            $formattedDate = $this->replaceMonthToNominative($formattedDate, $pattern, $locale);
        }

        return $formattedDate;
    }

    /**
     * Show date only
     *
     * @param \Datetime|string|int $date
     * @param string|null          $pattern
     * @param string|null          $locale
     * @param string|null          $timezone
     * @param string|null          $dateType
     *
     * @return string
     */
    public function formatDateOnly($date, $pattern = null, $locale = null, $timezone = null, $dateType = null)
    {
        if (null !== $pattern) {
            $pattern = trim(preg_replace('/[Hm:,]+/', '', $pattern));
        }

        return $this->formatDate($date, $pattern, $locale, $timezone, $dateType);
    }

    /**
     * Show (day and month)/season only
     *
     * @param \Datetime|string|int $date
     * @param string|null          $pattern
     * @param string|null          $locale
     * @param string|null          $timezone
     * @param string|null          $dateType
     *
     * @return string
     */
    public function formatDateDayMonth($date, $pattern = null, $locale = null, $timezone = null, $dateType = null)
    {
        if (null !== $pattern) {
            $pattern = trim(preg_replace('/[Hm:,Y]+/', '', $pattern));
        }

        return $this->formatDate($date, $pattern, $locale, $timezone, $dateType);
    }

    /**
     * @param \Datetime|string|int $time
     * @param string|null          $pattern
     * @param string|null          $locale
     * @param string|null          $timezone
     * @param string|null          $timeType
     *
     * @return string
     */
    public function formatTime($time, $pattern = null, $locale = null, $timezone = null, $timeType = null)
    {
        if ($pattern) {
            $this->checkConvertToSeason($pattern);
        }

        $formattedDate = $this->intlTwigDateTimeService->formatTime($time, $pattern, $locale, $timezone, $timeType);
        if (null !== $pattern) {
            $formattedDate = $this->replaceMonthToNominative($formattedDate, $pattern, $locale);
        }

        return $formattedDate;
    }

    /**
     * Show time only
     *
     * @param \Datetime|string|int $time
     * @param string|null          $pattern
     * @param string|null          $locale
     * @param string|null          $timezone
     * @param string|null          $timeType
     *
     * @return string
     */
    public function formatTimeOnly($time, $pattern = null, $locale = null, $timezone = null, $timeType = null)
    {
        if (null !== $pattern) {
            $pattern = 'HH:mm';
        }

        return $this->intlTwigDateTimeService->formatTime($time, $pattern, $locale, $timezone, $timeType);
    }

    /**
     * @param \Datetime|string|int $time
     * @param string|null          $pattern
     * @param string|null          $locale
     * @param string|null          $timezone
     * @param string|null          $dateType
     * @param string|null          $timeType
     *
     * @return string
     */
    public function formatDatetime($time, $pattern = null, $locale = null, $timezone = null, $dateType = null, $timeType = null)
    {
        if ($pattern) {
            $this->checkConvertToSeason($pattern);
        }

        $formattedDate = $this->intlTwigDateTimeService->formatDatetime($time, $pattern, $locale, $timezone, $dateType, $timeType);
        if (null !== $pattern) {
            $formattedDate = $this->replaceMonthToNominative($formattedDate, $pattern, $locale);
        }

        return $formattedDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'met_datetime';
    }

    /**
     * if $pattern have not day, than do Nominative month name in locale,
     * or, if it must be season, than replace month name with season name.
     *
     * @param string $formattedDate
     * @param string $pattern
     * @param string $locale
     *
     * @return mixed
     */
    private function replaceMonthToNominative($formattedDate, $pattern, $locale)
    {
        $result = $formattedDate;

        if (null !== $pattern &&
            isset($this->monthsAndSeasonsData[$locale]) &&
            false === strpos($pattern, 'd') &&
            false === strpos($pattern, 'j')
        ) {
            foreach ($this->monthsAndSeasonsData[$locale] as $key => $month) {
                if (false !== strpos($formattedDate, $key)) {
                    $result = str_replace($key, $month[(int) $this->monthsAndSeasonsArrayIndex], $formattedDate);
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Check for YEAR_SEASON_FORMAT in $pattern and replace it for full month name.
     *
     * @param string $pattern
     */
    private function checkConvertToSeason(&$pattern)
    {
        $this->monthsAndSeasonsArrayIndex = (int) (false !== strpos($pattern, self::YEAR_SEASON_FORMAT));
        if ($this->monthsAndSeasonsArrayIndex) {
            $pattern = str_replace(self::YEAR_SEASON_FORMAT, 'MMMM', $pattern);
        }
    }
}
