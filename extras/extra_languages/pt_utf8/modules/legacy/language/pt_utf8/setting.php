<?php
// Translation Info
// $Id$
// License http://creativecommons.org/licenses/by/2.5/br/
// ############################################################### //
// ## XOOPS Cube Legacy - Versão em Português
// ############################################################### //
// ## Por............: Mikhail Miguel
// ## Website........: http://xoops.net.br
// ## E-mail.........: mikhail.miguel@gmail.com
// ## AOL............: mikhailmiguel
// ## MSN............: mikhail.miguel@hotmail.com
// ## Orkut..........: 15440532260129226492
// ## Skype..........: mikhailmiguel
// ## Yahoo!.........: mikhailmiguel@yahoo.com
// ############################################################### //
// *************************************************************** //
/*

//%%%%% FORMATAÇÃO %%%%%

DIA --- ---
d -> Dia do mês, 2 digitos com preenchimento de zero 01 até 31
D -> Uma representação textual de um dia, três letras Mon até Sun
j -> Dia do mês sem preenchimento de zero 1 até 31
l -> ('L' minúsculo) A representação textual completa do dia da semana Sunday até Saturday
N -> Representação numérica ISO-8601 do dia da semana (acrescentado no PHP 5.1.0) 1 (para Segunda) até 7 (para Domingo)
S -> Sufixo ordinal inglês para o dia do mês, 2 caracteres st, nd, rd ou th. Funciona bem com j
w -> Representação numérica do dia da semana 0 (para domingo) até 6 (para sábado)
z -> O dia do ano (começando do 0) 0 through 365


SEMANA --- ---
W -> Número do ano da semana ISO-8601, semanas começam na Segunda (acrescentado no PHP 4.1.0) Exemplo: 42 (the 42nd week in the year)

MÊS --- ---

F -> Um representação completa de um mês, como January ou March January até December
m -> Representação numérica de um mês, com leading zeros 01 a 12
M -> Uma representação textual curta de um mês, três letras Jan a Dec
n -> Representação numérica de um mês, sem leading zeros 1 a 12
t -> Número de dias de um dado mês 28 through 31

ANO --- ---

L -> Se está em um ano bissexto 1 se está em ano bissexto, 0 caso contrário.
o -> Número do ano ISO-8601. Este tem o mesmo valor como Y, exceto que se o número da semana ISO (W) pertence ao anterior ou próximo ano, o ano é usado ao invés. (acrescentado no PHP 5.1.0) Exemplos: 1999 ou 2003
Y -> Uma representação de ano completa, 4 dígitos Exemplos: 1999 ou 2003
y -> Uma representação do ano com dois dígitos Exemplos: 99 ou 03

TEMPO --- ---
a -> Antes/Depois de meio-dia em minúsculo am or pm
A -> Antes/Depois de meio-dia em maiúsculo AM or PM
B -> Swatch internet time 000 até 999
g -> Formato 12-horas de uma hora sem preenchimento de zero 1 até 12
G -> Formato 24-horas de uma hora sem preenchimento de zero 0 até 23
h -> Formato 12-horas de uma hora com zero preenchendo à esquerda 01 até 12
H -> Formato 24-horas de uma hora com zero preenchendo à esquerda 00 até 23
i -> Minutos com zero preenchendo à esquerda 00 até 59
s -> Segundos, com zero preenchendo à esquerda 00 até 59
u -> Milisegundos (acrescentado no PHP 5.2.2) Exemplo: 54321


FUSO-HORÁRIO --- ---
e -> Identificador de Timezone (acrescentado no PHP 5.1.0) Exemplos: UTC, GMT, Atlantic/Azores
I -> (capital i) Se a data está ou não no horário de verão 1 se horário de verão, 0 caso contrário.
O -> Diferença para Greenwich time (GMT) em horas Exemplo: +0200
P -> Diferença para Greenwich time (GMT) com dois pontos entre horas e minutos (acrescentado no PHP 5.1.3) Exemplo: +02:00
T -> Abreviação de Timezone Exemplos: EST, MDT ...
Z -> Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. -43200 até 50400

FULL DATE/TIME --- ---

c -> ISO 8601 date (acrescentado no PHP 5) 2004-02-12T15:19:21+00:00
r -> » RFC 2822 formatted date Exemplo: Thu, 21 Dec 2000 16:01:07 +0200
U -> Segundos desde a Época Unix (January 1 1970 00:00:00 GMT) Veja também: time()

*/

//%%%%% TIME FORMAT SETTINGS %%%%%
if (!defined("_DATESTRING")) define("_DATESTRING","d/m/Y G:i:s");
if (!defined("_MEDIUMDATESTRING")) define("_MEDIUMDATESTRING","Y/n/j G:i");
if (!defined("_SHORTDATESTRING")) define("_SHORTDATESTRING","Y/n/j");
if (!defined("_JSDATEPICKSTRING")) define("_JSDATEPICKSTRING","yy-mm-dd");
if (!defined("_PHPDATEPICKSTRING")) define("_PHPDATEPICKSTRING","Y-m-d");

//%%%%% LANGUAGE SPECIFIC SETTINGS %%%%%
if (!defined("_CHARSET")) define("_CHARSET", "UTF-8");
if (!defined("_LANGCODE")) define("_LANGCODE", "pt");
// change 0 to 1 if this language is a multi-bytes language
if (!defined("XOOPS_USE_MULTIBYTES")) define("XOOPS_USE_MULTIBYTES", "0");

//%%%%% REQUESTED DATA SETTINGS %%%%%
if (!defined("_REQUESTED_DATA_NAME")) define("_REQUESTED_DATA_NAME", "requested_data_name");
if (!defined("_REQUESTED_ACTION_NAME")) define("_REQUESTED_ACTION_NAME", "requested_action_name");
if (!defined("_REQUESTED_DATA_ID")) define("_REQUESTED_DATA_ID", "requested_data_id");
?>