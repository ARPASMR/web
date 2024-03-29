<?php
# bg_BG translation for
# PHP-Calendar, DatePicker Calendar class: http://www.triconsole.com/php/calendar_datepicker.php
# Version: 2.30
# Language: Bulgarian
# Translator: Peter Petrov <peter.m.petrov@gmail.com>
# Last file update: 01.05.2010

// Class strings localization
define("L_DAY", "Ден");
define("L_MONTH", "Месец");
define("L_YEAR", "Година");
define("L_PREV", "Предишни");
define("L_NEXT", "Следващи");
define("L_REF_CAL", "Обновяване на Календара...");
define("L_CHK_VAL", "Проверете стойността");
define("L_SEL_LANG", "Изберете език");
define("L_SEL_ICON", "Изберете иконка");
define("L_SEL_DATE", "Изберете дата");
define("L_ERR_SEL", "Невалиден избор");
define("L_NOT_ALLOWED", "Тази дата не е позволена за избор");

// Set the first day of the week in your language
define("FIRST_DAY", "1"); // 1 for Monday, 0 for Sunday

// Months Long Names
define("L_JAN", "януари");
define("L_FEB", "февруари");
define("L_MAR", "март");
define("L_APR", "април");
define("L_MAY", "май");
define("L_JUN", "юни");
define("L_JUL", "юли");
define("L_AUG", "август");
define("L_SEP", "септември");
define("L_OCT", "октомври");
define("L_NOV", "ноември");
define("L_DEC", "декември");
// Months Short Names
define("L_S_JAN", "ян.");
define("L_S_FEB", "фев.");
define("L_S_MAR", "март");
define("L_S_APR", "апр.");
define("L_S_MAY", "май");
define("L_S_JUN", "юни");
define("L_S_JUL", "юли");
define("L_S_AUG", "авг.");
define("L_S_SEP", "сеп.");
define("L_S_OCT", "окт.");
define("L_S_NOV", "ное.");
define("L_S_DEC", "дек.");
// Week days Long Names
define("L_MON", "понеделник");
define("L_TUE", "вторник");
define("L_WED", "сряда");
define("L_THU", "четвъртък");
define("L_FRI", "петък");
define("L_SAT", "събота");
define("L_SUN", "неделя");
// Week days Short Names
define("L_S_MON", "пон");
define("L_S_TUE", "вт");
define("L_S_WED", "ср");
define("L_S_THU", "четв");
define("L_S_FRI", "пет");
define("L_S_SAT", "съб");
define("L_S_SUN", "нед");

// Windows encoding
define("WIN_DEFAULT", "windows-1251");
if(!defined("L_LANG") || L_LANG == "L_LANG") define("L_LANG", "bg_BG");

// Set the BG specific date/time format
if (stristr(PHP_OS,"win")) {
setlocale(LC_ALL, "bulgarian.UTF-8", "bulgarian.UTF-8@euro", "bul_bul.UTF-8", "bul.UTF-8", "bgr.UTF-8", "bulgarian");
} else {
setlocale(LC_ALL, "bg_BG.UTF-8", "bg_BG.UTF-8@euro", "bul_bul.UTF-8", "bul.UTF-8", "bgr_BGR.UTF-8", "bgr.UTF-8", "bulgarian.UTF-8"); // For BG formats
}
?>