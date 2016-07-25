<?php
namespace Larakit\QuickForm;

abstract class ElementDatetimepickerTwbs extends \HTML_QuickForm2_Element_InputText {

    use TraitNode;

    /**
     * @param string $format DD.MM.YYYY HH:mm
     *
     * @return $this
     */
    function setFormat($format) {
        $this->setAttribute('data-dp-format', $format);

        return $this;
    }

    /**
     * @param string $dayViewHeaderFormat разрешено: date, moment, string
     *
     * @return $this
     */
    function setDayViewHeaderFormat($dayViewHeaderFormat) {
        $this->setAttribute('data-dp-dayViewHeaderFormat', $dayViewHeaderFormat);

        return $this;
    }

    /**
     * Шаг при переходе по стрелке в календаре
     *
     * @param $stepping
     *
     * @return $this
     */
    function setStepping($stepping = 1) {
        $this->setAttribute('data-dp-stepping', $stepping);

        return $this;
    }

    function setMinDate($minDate) {
        $this->setAttribute('data-dp-minDate', $minDate);

        return $this;
    }

    function setMaxDate($maxDate) {
        $this->setAttribute('data-dp-maxDate', $maxDate);

        return $this;
    }

    function setUseCurrent($useCurrent) {
        $this->setAttribute('data-dp-useCurrent', (bool) $useCurrent);

        return $this;
    }

    function setCollapse($collapse) {
        $this->setAttribute('data-dp-collapse', $collapse);

        return $this;
    }

    function setLocale($locale) {
        $this->setAttribute('data-dp-locale', $locale);

        return $this;
    }

    function setDefaultDate($defaultDate) {
        $this->setAttribute('data-dp-defaultDate', $defaultDate);

        return $this;
    }

    function setDisabledDates($disabledDates) {
        $this->setAttribute('data-dp-disabledDates', $disabledDates);

        return $this;
    }

    function setEnabledDates($enabledDates) {
        $this->setAttribute('data-dp-enabledDates', $enabledDates);

        return $this;
    }

    function setIcons($icons) {
        $this->setAttribute('data-dp-icons', $icons);

        return $this;
    }

    function setUseStrict($useStrict) {
        $this->setAttribute('data-dp-useStrict', $useStrict);

        return $this;
    }

    function setSideBySide($sideBySide) {
        $this->setAttribute('data-dp-sideBySide', $sideBySide);

        return $this;
    }

    function setDaysOfWeekDisabled($daysOfWeekDisabled) {
        $this->setAttribute('data-dp-daysOfWeekDisabled', $daysOfWeekDisabled);

        return $this;
    }

    function setCalendarWeeks($calendarWeeks) {
        $this->setAttribute('data-dp-calendarWeeks', $calendarWeeks);

        return $this;
    }

    function setViewMode($viewMode) {
        $this->setAttribute('data-dp-viewMode', $viewMode);

        return $this;
    }

    function setToolbarPlacement($toolbarPlacement) {
        $this->setAttribute('data-dp-toolbarPlacement', $toolbarPlacement);

        return $this;
    }

    function setShowTodayButton($showTodayButton) {
        $this->setAttribute('data-dp-showTodayButton', $showTodayButton);

        return $this;
    }

    function setShowClear($showClear) {
        $this->setAttribute('data-dp-showClear', $showClear);

        return $this;
    }

    function setShowClose($showClose) {
        $this->setAttribute('data-dp-showClose', $showClose);

        return $this;
    }

    function setWidgetPositioning($widgetPositioning) {
        $this->setAttribute('data-dp-widgetPositioning', $widgetPositioning);

        return $this;
    }

    function setWidgetParent($widgetParent) {
        $this->setAttribute('data-dp-widgetParent', $widgetParent);

        return $this;
    }

    function setKeepOpen($keepOpen) {
        $this->setAttribute('data-dp-keepOpen', $keepOpen);

        return $this;
    }

    function setInline($inline) {
        $this->setAttribute('data-dp-inline', $inline);

        return $this;
    }

    function setKeepInvalid($keepInvalid) {
        $this->setAttribute('data-dp-keepInvalid', $keepInvalid);

        return $this;
    }

    function setKeyBinds($keyBinds) {
        $this->setAttribute('data-dp-keyBinds', $keyBinds);

        return $this;
    }

    function setDebug($debug) {
        $this->setAttribute('data-dp-debug', $debug);

        return $this;
    }

    function setDisabledTimeIntervals($disabledTimeIntervals) {
        $this->setAttribute('data-dp-disabledTimeIntervals', $disabledTimeIntervals);

        return $this;
    }

    function setAllowInputToggle($allowInputToggle) {
        $this->setAttribute('data-dp-allowInputToggle', $allowInputToggle);

        return $this;
    }

    function setFocusOnShow($focusOnShow) {
        $this->setAttribute('data-dp-focusOnShow', $focusOnShow);

        return $this;
    }

    function setEnabledHours($enabledHours) {
        $this->setAttribute('data-dp-enabledHours', $enabledHours);

        return $this;
    }

    function setDisabledHours($disabledHours) {
        $this->setAttribute('data-dp-disabledHours', $disabledHours);

        return $this;
    }

    function setViewDate($viewDate) {
        $this->setAttribute('data-dp-viewDate', $viewDate);

        return $this;
    }

    function setTooltips($tooltips) {
        $this->setAttribute('data-dp-tooltips', $tooltips);

        return $this;
    }

}