<?php
// Translation Info
// $Id$
// License http://creativecommons.org/licenses/by/2.5/br/
// ############################################################### //
// ## XOOPS Cube Legacy - Versгo em Portuguкs
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
//%%%%% FORMATAЗГO %%%%%
DIA
d -> Dia do mкs, 2 digitos com preenchimento de zero 01 atй 31
D -> Uma representaзгo textual de um dia, trкs letras Mon atй Sun
j -> Dia do mкs sem preenchimento de zero 1 atй 31
l -> ('L' minъsculo) A representaзгo textual completa do dia da semana Sunday atй Saturday
N -> Representaзгo numйrica ISO-8601 do dia da semana (acrescentado no PHP 5.1.0) 1 (para Segunda) atй 7 (para Domingo)
S -> Sufixo ordinal inglкs para o dia do mкs, 2 caracteres st, nd, rd ou th. Funciona bem com j
w -> Representaзгo numйrica do dia da semana 0 (para domingo) atй 6 (para sбbado)
z -> O dia do ano (comeзando do 0) 0 through 365

SEMANA --- ---
W -> Nъmero do ano da semana ISO-8601, semanas comeзam na Segunda (acrescentado no PHP 4.1.0) Exemplo: 42 (the 42nd week in the year)
MКS --- ---
F -> Um representaзгo completa de um mкs, como January ou March January atй December
m -> Representaзгo numйrica de um mкs, com leading zeros 01 a 12
M -> Uma representaзгo textual curta de um mкs, trкs letras Jan a Dec
n -> Representaзгo numйrica de um mкs, sem leading zeros 1 a 12
t -> Nъmero de dias de um dado mкs 28 through 31
ANO --- ---
L -> Se estб em um ano bissexto 1 se estб em ano bissexto, 0 caso contrбrio.
o -> Nъmero do ano ISO-8601. Este tem o mesmo valor como Y, exceto que se o nъmero da semana ISO (W) pertence ao anterior ou prуximo ano, o ano й usado ao invйs. (acrescentado no PHP 5.1.0) Exemplos: 1999 ou 2003
Y -> Uma representaзгo de ano completa, 4 dнgitos Exemplos: 1999 ou 2003
y -> Uma representaзгo do ano com dois dнgitos Exemplos: 99 ou 03
TEMPO --- ---
a -> Antes/Depois de meio-dia em minъsculo am or pm
A -> Antes/Depois de meio-dia em maiъsculo AM or PM
B -> Swatch internet time 000 atй 999
g -> Formato 12-horas de uma hora sem preenchimento de zero 1 atй 12
G -> Formato 24-horas de uma hora sem preenchimento de zero 0 atй 23
h -> Formato 12-horas de uma hora com zero preenchendo а esquerda 01 atй 12
H -> Formato 24-horas de uma hora com zero preenchendo а esquerda 00 atй 23
i -> Minutos com zero preenchendo а esquerda 00 atй 59
s -> Segundos, com zero preenchendo а esquerda 00 atй 59
u -> Milisegundos (acrescentado no PHP 5.2.2) Exemplo: 54321

FUSO-HORБRIO --- ---
e -> Identificador de Timezone (acrescentado no PHP 5.1.0) Exemplos: UTC, GMT, Atlantic/Azores
I -> (capital i) Se a data estб ou nгo no horбrio de verгo 1 se horбrio de verгo, 0 caso contrбrio.
O -> Diferenзa para Greenwich time (GMT) em horas Exemplo: +0200
P -> Diferenзa para Greenwich time (GMT) com dois pontos entre horas e minutos (acrescentado no PHP 5.1.3) Exemplo: +02:00
T -> Abreviaзгo de Timezone Exemplos: EST, MDT ...
Z -> Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. -43200 atй 50400
FULL DATE/TIME --- ---
c -> ISO 8601 date (acrescentado no PHP 5) 2004-02-12T15:19:21+00:00
r -> » RFC 2822 formatted date Exemplo: Thu, 21 Dec 2000 16:01:07 +0200
U -> Segundos desde a Йpoca Unix (January 1 1970 00:00:00 GMT) Veja tambйm time()
*/
//%%%%% TIME FORMAT SETTINGS %%%%%
if (!defined("_DATESTRING")) define("_DATESTRING","d/m/Y G:i:s");
if (!defined("_MEDIUMDATESTRING")) define("_MEDIUMDATESTRING","Y/n/j G:i");
if (!defined("_SHORTDATESTRING")) define("_SHORTDATESTRING","Y/n/j");
if (!defined("_JSDATEPICKSTRING")) define("_JSDATEPICKSTRING","yy-mm-dd");
if (!defined("_PHPDATEPICKSTRING")) define("_PHPDATEPICKSTRING","Y-m-d");
//%%%%% LANGUAGE SPECIFIC SETTINGS %%%%%
if (!defined("_CHARSET")) define("_CHARSET", "ISO-8859-1");
if (!defined("_LANGCODE")) define("_LANGCODE", "pt");
// change 0 to 1 if this language is a multi-bytes language
if (!defined("XOOPS_USE_MULTIBYTES")) define("XOOPS_USE_MULTIBYTES", "0");
//%%%%% REQUESTED DATA SETTINGS %%%%%
if (!defined("_REQUESTED_DATA_NAME")) define("_REQUESTED_DATA_NAME", "requested_data_name");
if (!defined("_REQUESTED_ACTION_NAME")) define("_REQUESTED_ACTION_NAME", "requested_action_name");
if (!defined("_REQUESTED_DATA_ID")) define("_REQUESTED_DATA_ID", "requested_data_id");
?>