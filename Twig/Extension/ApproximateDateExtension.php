<?php

namespace Metalslave\ApproximateDateBundle\Twig\Extension;

use Sonata\IntlBundle\Twig\Extension\DateTimeExtension;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ApproximateDateExtension for replace months name to nominative or season.
 */
class ApproximateDateExtension extends \Twig_Extension
{
    /** @var DateTimeExtension */
    private $intlTwigDateTimeService;

    /** @var int */
    private $monthsAndSeasonsArrayIndex;

    /** @var TranslatorInterface */
    private $translator;

    /** @var string */
    private $replaceChars = '_!?*#&$@~';

    /** @var string */
    private $selectedChar = null;

    const YEAR_SEASON_FORMAT = 'S';

    /**
     * AppDateTimeExtension constructor.
     *
     * @param DateTimeExtension   $intlTwigDateTimeService
     * @param TranslatorInterface $translator
     */
    public function __construct($intlTwigDateTimeService, TranslatorInterface $translator)
    {
        $this->intlTwigDateTimeService = $intlTwigDateTimeService;
        $this->translator = $translator;
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
     * Show date only.
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
     * Show (day and month)/season only.
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
     * Show time only.
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

        if (null !== $pattern && false === strpos($pattern, 'd')) {
            //translation does not return the correct value if there is a character ':' in the initial string
            if (false !== strpos($formattedDate, ':')) {
                $this->replaceDoubleDots($formattedDate);
            }
            $dataWords = explode(' ', $formattedDate);
            foreach ($dataWords as $key => $word) {
                $dataWords[$key] = $this->translator->transChoice(
                    $word,
                    (int) $this->monthsAndSeasonsArrayIndex,
                    [],
                    'MetalslaveApproximateDateBundle',
                    $locale
                );
            }
            $result = implode(' ', $dataWords);
            $this->backupDoubleDots($result);
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

    /**
     * @param mixed $str
     */
    private function replaceDoubleDots(&$str)
    {
        $chars = str_split($this->replaceChars);
        foreach ($chars as $char) {
            if (false === strpos($str, $char)) {
                $this->selectedChar = $char;
                $str = str_replace(':', $this->selectedChar, $str);
                break;
            }
        }
    }

    /**
     * @param mixed $str
     */
    private function backupDoubleDots(&$str)
    {
        if (null !== $this->selectedChar) {
            $str = str_replace($this->selectedChar, ':', $str);
        }
    }
}
