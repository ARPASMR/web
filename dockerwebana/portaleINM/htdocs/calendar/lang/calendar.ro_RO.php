<?php
# ro_RO translation for
# PHP-Calendar, DatePicker Calendar class: http://www.triconsole.com/php/calendar_datepicker.php
# Version: 2.30
# Language: Romanian
# Translator: Ciprian Murariu <ciprianmp@yahoo.com>
# Last file update: 01.05.2010

// Class strings localization
define("L_DAY", "Ziua");
define("L_MONTH", "Luna");
define("L_YEAR", "Anul");
define("L_PREV", "Înapoi");
define("L_NEXT", "Înainte");
define("L_CHK_VAL", "Verifică valoarea");
define("L_SEL_LANG", "Alege Limba");
define("L_SEL_ICON", "Alege Icon-ul");
define("L_SEL_DATE", "Alege data");
define("L_REF_CAL", "Calendarul se reiniţializează...");
define("L_ERR_SEL", "Data selectată nu este validă");
define("L_NOT_ALLOWED", "Nu este permisă selectarea acestei date");

// Set the first day of the week in your language (0 for Sunday, 1 for Monday)
define("FIRST_DAY", "1");

// Months Long Names
define("L_JAN", "Ianuarie");
define("L_FEB", "Februarie");
define("L_MAR", "Martie");
define("L_APR", "Aprilie");
define("L_MAY", "Mai");
define("L_JUN", "Iunie");
define("L_JUL", "Iulie");
define("L_AUG", "August");
define("L_SEP", "Septembrie");
define("L_OCT", "Octombrie");
define("L_NOV", "Noiembrie");
define("L_DEC", "Decembrie");
// Months Short Names
define("L_S_JAN", "Ian");
define("L_S_FEB", "Feb");
define("L_S_MAR", "Mar");
define("L_S_APR", "Apr");
define("L_S_MAY", "Mai");
define("L_S_JUN", "Iun");
define("L_S_JUL", "Iul");
define("L_S_AUG", "Aug");
define("L_S_SEP", "Sept");
define("L_S_OCT", "Oct");
define("L_S_NOV", "Nov");
define("L_S_DEC", "Dec");
// Week days Long Names
define("L_MON", "Luni");
define("L_TUE", "Marţi");
define("L_WED", "Miercuri");
define("L_THU", "Joi");
define("L_FRI", "Vineri");
define("L_SAT", "Sâmbătă");
define("L_SUN", "Duminică");
// Week days Short Names
define("L_S_MON", "L");
define("L_S_TUE", "Ma");
define("L_S_WED", "Mi");
define("L_S_THU", "J");
define("L_S_FRI", "V");
define("L_S_SAT", "S");
define("L_S_SUN", "D");

// Windows encoding
define("WIN_DEFAULT", "windows-1250");
if(!defined("L_LANG") || L_LANG == "L_LANG") define("L_LANG", "ro_RO");

// Set the RO specific date/time format
if (stristr(PHP_OS,"win")) {
setlocale(LC_ALL, "ROU_ROU.UTF-8", "ROU_ROU", "romanian.UTF-8", "romanian"); // For Windows servers
} else {
setlocale(LC_ALL, "ro_RO.UTF-8@euro", "ro_RO.UTF-8", "rou.UTF-8", "rou_rou.UTF-8"); // For Unix/FreeBSD servers
}
?>