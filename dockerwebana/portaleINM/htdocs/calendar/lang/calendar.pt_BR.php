<?php
# pt_BR translation for
# PHP-Calendar, DatePicker Calendar class: http://www.triconsole.com/php/calendar_datepicker.php
# Version: 2.30
# Language: Português (Brasil)
# Translator: Jaime Dias ( jaime@jotadias.com.br)
# Last file update: 29.07.2010

// Class strings localization
define("L_DAY", "Dia");
define("L_MONTH", "Mês");
define("L_YEAR", "Ano");
define("L_PREV", "Anterior");
define("L_NEXT", "Próximo");
define("L_REF_CAL", "Atualizando...");
define("L_CHK_VAL", "Verifique data");
define("L_SEL_LANG", "Selecione Idioma");
define("L_SEL_ICON", "Selecione Icon");
define("L_SEL_DATE", "Selecione Data");
define("L_ERR_SEL", "Sua Seleção não é válida");
define("L_NOT_ALLOWED", "Esta data não é permitida ser selecionada");

// Set the first day of the week in your language (0 for Sunday, 1 for Monday)
define("FIRST_DAY", "1");

// Months Long Names
define("L_JAN", "Janneiro");
define("L_FEB", "Fevereiro");
define("L_MAR", "Março");
define("L_APR", "Abril");
define("L_MAY", "Maio");
define("L_JUN", "Junho");
define("L_JUL", "Julho");
define("L_AUG", "Agosto");
define("L_SEP", "Setembro");
define("L_OCT", "Outubro");
define("L_NOV", "Novembro");
define("L_DEC", "Dezembro");
// Months Short Names
define("L_S_JAN", "Jan");
define("L_S_FEB", "Fev");
define("L_S_MAR", "Mar");
define("L_S_APR", "Abr");
define("L_S_MAY", "Mai");
define("L_S_JUN", "Jun");
define("L_S_JUL", "Jul");
define("L_S_AUG", "Ago");
define("L_S_SEP", "Set");
define("L_S_OCT", "Out");
define("L_S_NOV", "Nov");
define("L_S_DEC", "Dez");
// Week days Long Names
define("L_MON", "Segunda");
define("L_TUE", "Terça");
define("L_WED", "Quarta");
define("L_THU", "Quinta");
define("L_FRI", "Sexta");
define("L_SAT", "Sabado");
define("L_SUN", "Domingo");
// Week days Short Names
define("L_S_MON", "Seg");
define("L_S_TUE", "Ter");
define("L_S_WED", "Qua");
define("L_S_THU", "Qui");
define("L_S_FRI", "Sex");
define("L_S_SAT", "Sab");
define("L_S_SUN", "Dom");

// Windows encoding
define("WIN_DEFAULT", "windows-1252");
define("L_CAL_FORMAT", "%d %B %Y");
if(!defined("L_LANG") || L_LANG == "L_LANG") define("L_LANG", "pt_BR"); // en_US format of your language

// Set the US specific date/time format
if (stristr(PHP_OS,"win")) {
setlocale(LC_ALL, "pt-BR.UTF-8", "pt-br", "portuguese");
} else {
setlocale(LC_ALL, "pt_BR.UTF-8", "por.UTF-8", "por_por.UTF-8", "Portuguese.UTF-8");
}
?>