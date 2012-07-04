<?php
/**
* $Id$
* XOOPS Cube Legacy 2.2 - M�dulo LeCAT - Tradu��o para o Portugu�s
* Traduzido por Mikhail Miguel < mailto:mikhail.miguel@gmail.com >
* http://xoops.net.br/ | http://about.me/mikhail.miguel
* Esta tradu��o encontra-se licenciada sob a licen�a Creative Commons Attribution 2.5 Brazil.
* Para ler uma c�pia da licen�a, visite: http://creativecommons.org/licenses/by/2.5/br/
**/

define("_MD_LECAT_DESC_PERMISSION_TYPE","Configura��o dos tipos de permiss�es e os seus respectivos valores pr�-definidos.");
define("_MD_LECAT_ERROR_CONTENT_IS_NOT_FOUND","O conte�do requisitado n�o foi encontrado");
define("_MD_LECAT_ERROR_DBUPDATE_FAILED","Ocorreu um erro ao tentar atualizar o banco de dados.");
define("_MD_LECAT_ERROR_EMAIL","{0} � um endere�o de e-mail inv�lido.");
define("_MD_LECAT_ERROR_EXTENSION","A extens�o do arquivo inserido n�o corresponde a qualquer entrada na lista de tipos permitidos.");
define("_MD_LECAT_ERROR_HAS_CHILDREN", "Aten��o: para remover esta categoria, remova todas as suas respectivas subcategorias ou acrescente <code>&amp;force=1</code> � requisi��o GET.");
define("_MD_LECAT_ERROR_HAS_CLIENT_DATA", "N�o foi poss�vel remover esta categoria pois ela est� vinculada a outros dados que impedem a sua remo��o. Elimine esses v�nculos antes de tentar remover esta categoria de novo.");
define("_MD_LECAT_ERROR_INTRANGE","Entrada incorreta em {0}.");
define("_MD_LECAT_ERROR_MAX","Entrada {0} com um valor num�rico igual ou menor que {1}.");
define("_MD_LECAT_ERROR_MAXLENGTH","Entrada {0} com {1} ou menos caracteres.");
define("_MD_LECAT_ERROR_MIN","Entrada {0} com um valor num�rico igual ou maior que {1}.");
define("_MD_LECAT_ERROR_MINLENGTH","Entrada {0} com {1} ou mais caracteres.");
define("_MD_LECAT_ERROR_NO_CATEGORY_REQUESTED","Nenhuma categoria foi solicitada");
define("_MD_LECAT_ERROR_OBJECTEXIST","Entrada incorreta em {0}.");
define("_MD_LECAT_ERROR_REQUIRED","{0} � um valor necess�rio para a realiza��o desta a��o.");
define("_MD_LECAT_LANG_ACTIONS","A��o");
define("_MD_LECAT_LANG_ADD_A_NEW_CAT","Acrescentar uma nova categoria");
define("_MD_LECAT_LANG_ADD_A_NEW_PERMISSION_TYPE","Acrescentar um novo tipo de permiss�o");
define("_MD_LECAT_LANG_ADD_A_NEW_PERMIT","Definir uma nova permiss�o");
define("_MD_LECAT_LANG_ADD_A_NEW_SET","Acrescentar um novo conjunto de categorias");
define("_MD_LECAT_LANG_AUTH_DEFAULT","Valor padr�o da autoriza��o");
define("_MD_LECAT_LANG_AUTH_KEY","Nome da chave da autoriza��o");
define("_MD_LECAT_LANG_AUTH_SETTING","Defini��o da autoriza��o");
define("_MD_LECAT_LANG_AUTH_TITLE","T�tulo de exibi��o da autoriza��o");
define("_MD_LECAT_LANG_CAT","Categoria");
define("_MD_LECAT_LANG_CAT_DELETE","Remover categoria");
define("_MD_LECAT_LANG_CAT_EDIT","Editar categoria");
define("_MD_LECAT_LANG_CAT_ID","CAT_ID");
define("_MD_LECAT_LANG_CATEGORY","Categoria");
define("_MD_LECAT_LANG_CONTROL","Controle");
define("_MD_LECAT_LANG_DEFAULT_PERMISSIONS","Conjunto de defini��es padr�o");
define("_MD_LECAT_LANG_DELEET_ALL_PERMIT","Remover todas as permiss�es desta categoria");
define("_MD_LECAT_LANG_DEPTH","Profundidade");
define("_MD_LECAT_LANG_DESCRIPTION","Descri��o");
define("_MD_LECAT_LANG_EDIT_ACTOR","Editar atores");
define("_MD_LECAT_LANG_GROUPID","N�mero do grupo");
define("_MD_LECAT_LANG_LEVEL","Profundidade m�xima");
define("_MD_LECAT_LANG_LEVEL_UNLIMITED","Profundidade ilimitada");
define("_MD_LECAT_LANG_MANAGER","Gerente");
define("_MD_LECAT_LANG_MODULES","M�dulos");
define("_MD_LECAT_LANG_MODULES_CONFINEMENT","Confinamento de m�dulo");
define("_MD_LECAT_LANG_OPTIONS","Op��o");
define("_MD_LECAT_LANG_P_ID","N�mero da categoria anterior");
define("_MD_LECAT_LANG_PARENT","Categoria secund�ria");
define("_MD_LECAT_LANG_PERMISSION_ON","Permitido");
define("_MD_LECAT_LANG_PERMISSION_TYPE","Tipo de permiss�o");
define("_MD_LECAT_LANG_PERMISSIONS","Permiss�es");
define("_MD_LECAT_LANG_PERMIT_DELETE","Permitir remo��es");
define("_MD_LECAT_LANG_PERMIT_EDIT","Permitir edi��es");
define("_MD_LECAT_LANG_PERMIT_ID","PERMIT_ID");
define("_MD_LECAT_LANG_POSTER","Autor");
define("_MD_LECAT_LANG_SET","Defini��es da categoria ");
define("_MD_LECAT_LANG_SET_DELETE","Remover");
define("_MD_LECAT_LANG_SET_EDIT","Editar");
define("_MD_LECAT_LANG_SET_ID","Definir ID");
define("_MD_LECAT_LANG_TITLE","T�tulo");
define("_MD_LECAT_LANG_TOP_CAT","Categoria principal");
define("_MD_LECAT_LANG_UID","UID");
define("_MD_LECAT_LANG_VIEWER","Leitor");
define("_MD_LECAT_LANG_WEIGHT","Peso");
define("_MD_LECAT_MESSAGE_CONFIRM_DELETE","Tem certeza que deseja remover?");
define("_MD_LECAT_MESSAGE_CONFIRM_SET_DELETE","Tem certeza que deseja remover? Todas as categorias pertencentes a esta categoria de jogos s�o removidos tamb�m.");
define("_MD_LECAT_TIPS_CATEGORY_SET","<p>Lecat � um m�dulo de gerenciamento de categorias.<br />Isto significa que outros m�dulos (como os de f�runs, not�cias, artigos, etc) podem utilizar o Lecat como mecanismo de gest�o de suas pr�prias categorias.<br /></p><p>Lecat fornece duas fun��es principais a outros m�dulos:</p><ul><li>Lista de Categorias (como uma �rvore)</li><li>Verifica��o de permiss�o para cada categoria.</li></ul></p><h3>Conjunto de categorias</h3><p>Cada m�dulo requer categorias pr�prias. Por exemplo, um m�dulo sobre as novidades do portal requer categorias do tipo 'Atualiza��es', 'Novos associados', 'Novas mensagens'. Por outro lado, um m�dulo de f�rum pode utilizar categorias como 'Perguntas', 'Solicita��es', 'Conversas'. <br /> Assim, voc� pode criar conjuntos de diversas categorias. Para not�cias, f�runs, etc. </p><p>Nesse caso, voc� dever� instalar uma c�pia do m�dulo Lecat e criar um novo conjunto de categorias.</p>");
define("_MD_LECAT_TIPS_LEVEL","Max profundidade na �rvore de categorias. Definir como <q>0</q> (zero) significa <q>sem limite</q>.");
define("_MD_LECAT_TIPS_MODULE_CONFINEMENT","Definir o nome do m�dulo separados por v�rgulas se voc� quiser aplicar esta categoria em m�dulos espec�ficos somente.");
define("_MD_LECAT_TIPS_PERMISSIONS","Deixe as permiss�es a seguir se voc� deseja que esta subcategoria herde as permiss�es da categoria principal.");
?>