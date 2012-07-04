<?php
// Translation Info
// $Id$
// License http://creativecommons.org/licenses/by/2.5/br/
// ############################################################### //
// ## XOOPS Cube Legacy - Vers�o em Portugu�s
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
//%%%%% FORMATA��O %%%%%
DIA
d -> Dia do m�s, 2 digitos com preenchimento de zero 01 at� 31
D -> Uma representa��o textual de um dia, tr�s letras Mon at� Sun
j -> Dia do m�s sem preenchimento de zero 1 at� 31
l -> ('L' min�sculo) A representa��o textual completa do dia da semana Sunday at� Saturday
N -> Representa��o num�rica ISO-8601 do dia da semana (acrescentado no PHP 5.1.0) 1 (para Segunda) at� 7 (para Domingo)
S -> Sufixo ordinal ingl�s para o dia do m�s, 2 caracteres st, nd, rd ou th. Funciona bem com j
w -> Representa��o num�rica do dia da semana 0 (para domingo) at� 6 (para s�bado)
z -> O dia do ano (come�ando do 0) 0 through 365

SEMANA --- ---
W -> N�mero do ano da semana ISO-8601, semanas come�am na Segunda (acrescentado no PHP 4.1.0) Exemplo: 42 (the 42nd week in the year)
M�S --- ---
F -> Um representa��o completa de um m�s, como January ou March January at� December
m -> Representa��o num�rica de um m�s, com leading zeros 01 a 12
M -> Uma representa��o textual curta de um m�s, tr�s letras Jan a Dec
n -> Representa��o num�rica de um m�s, sem leading zeros 1 a 12
t -> N�mero de dias de um dado m�s 28 through 31
ANO --- ---
L -> Se est� em um ano bissexto 1 se est� em ano bissexto, 0 caso contr�rio.
o -> N�mero do ano ISO-8601. Este tem o mesmo valor como Y, exceto que se o n�mero da semana ISO (W) pertence ao anterior ou pr�ximo ano, o ano � usado ao inv�s. (acrescentado no PHP 5.1.0) Exemplos: 1999 ou 2003
Y -> Uma representa��o de ano completa, 4 d�gitos Exemplos: 1999 ou 2003
y -> Uma representa��o do ano com dois d�gitos Exemplos: 99 ou 03
TEMPO --- ---
a -> Antes/Depois de meio-dia em min�sculo am or pm
A -> Antes/Depois de meio-dia em mai�sculo AM or PM
B -> Swatch internet time 000 at� 999
g -> Formato 12-horas de uma hora sem preenchimento de zero 1 at� 12
G -> Formato 24-horas de uma hora sem preenchimento de zero 0 at� 23
h -> Formato 12-horas de uma hora com zero preenchendo � esquerda 01 at� 12
H -> Formato 24-horas de uma hora com zero preenchendo � esquerda 00 at� 23
i -> Minutos com zero preenchendo � esquerda 00 at� 59
s -> Segundos, com zero preenchendo � esquerda 00 at� 59
u -> Milisegundos (acrescentado no PHP 5.2.2) Exemplo: 54321

FUSO-HOR�RIO --- ---
e -> Identificador de Timezone (acrescentado no PHP 5.1.0) Exemplos: UTC, GMT, Atlantic/Azores
I -> (capital i) Se a data est� ou n�o no hor�rio de ver�o 1 se hor�rio de ver�o, 0 caso contr�rio.
O -> Diferen�a para Greenwich time (GMT) em horas Exemplo: +0200
P -> Diferen�a para Greenwich time (GMT) com dois pontos entre horas e minutos (acrescentado no PHP 5.1.3) Exemplo: +02:00
T -> Abrevia��o de Timezone Exemplos: EST, MDT ...
Z -> Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. -43200 at� 50400
FULL DATE/TIME --- ---
c -> ISO 8601 date (acrescentado no PHP 5) 2004-02-12T15:19:21+00:00
r -> � RFC 2822 formatted date Exemplo: Thu, 21 Dec 2000 16:01:07 +0200
U -> Segundos desde a �poca Unix (January 1 1970 00:00:00 GMT) Veja tamb�m time()
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