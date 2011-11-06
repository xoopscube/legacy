<?php
/**
* $Id$
* XOOPS Cube Legacy 2.2 - Módulo LeCAT - Tradução para o Português
* Traduzido por Mikhail Miguel < mailto:mikhail.miguel@gmail.com >
* http://xoops.net.br/ | http://about.me/mikhail.miguel
* Esta tradução encontra-se licenciada sob a licença Creative Commons Attribution 2.5 Brazil.
* Para ler uma cópia da licença, visite: http://creativecommons.org/licenses/by/2.5/br/
**/

define("_MD_LECAT_DESC_PERMISSION_TYPE","Configuração dos tipos de permissões e os seus respectivos valores pré-definidos.");
define("_MD_LECAT_ERROR_CONTENT_IS_NOT_FOUND","O conteúdo requisitado não foi encontrado");
define("_MD_LECAT_ERROR_DBUPDATE_FAILED","Ocorreu um erro ao tentar atualizar o banco de dados.");
define("_MD_LECAT_ERROR_EMAIL","{0} é um endereço de e-mail inválido.");
define("_MD_LECAT_ERROR_EXTENSION","A extensão do arquivo inserido não corresponde a qualquer entrada na lista de tipos permitidos.");
define("_MD_LECAT_ERROR_HAS_CHILDREN", "Atenção: para remover esta categoria, remova todas as suas respectivas subcategorias ou acrescente <code>&amp;force=1</code> à requisição GET.");
define("_MD_LECAT_ERROR_HAS_CLIENT_DATA", "Não foi possível remover esta categoria pois ela está vinculada a outros dados que impedem a sua remoção. Elimine esses vínculos antes de tentar remover esta categoria de novo.");
define("_MD_LECAT_ERROR_INTRANGE","Entrada incorreta em {0}.");
define("_MD_LECAT_ERROR_MAX","Entrada {0} com um valor numérico igual ou menor que {1}.");
define("_MD_LECAT_ERROR_MAXLENGTH","Entrada {0} com {1} ou menos caracteres.");
define("_MD_LECAT_ERROR_MIN","Entrada {0} com um valor numérico igual ou maior que {1}.");
define("_MD_LECAT_ERROR_MINLENGTH","Entrada {0} com {1} ou mais caracteres.");
define("_MD_LECAT_ERROR_NO_CATEGORY_REQUESTED","Nenhuma categoria foi solicitada");
define("_MD_LECAT_ERROR_OBJECTEXIST","Entrada incorreta em {0}.");
define("_MD_LECAT_ERROR_REQUIRED","{0} é um valor necessário para a realização desta ação.");
define("_MD_LECAT_LANG_ACTIONS","Ação");
define("_MD_LECAT_LANG_ADD_A_NEW_CAT","Acrescentar uma nova categoria");
define("_MD_LECAT_LANG_ADD_A_NEW_PERMISSION_TYPE","Acrescentar um novo tipo de permissão");
define("_MD_LECAT_LANG_ADD_A_NEW_PERMIT","Definir uma nova permissão");
define("_MD_LECAT_LANG_ADD_A_NEW_SET","Acrescentar um novo conjunto de categorias");
define("_MD_LECAT_LANG_AUTH_DEFAULT","Valor padrão da autorização");
define("_MD_LECAT_LANG_AUTH_KEY","Nome da chave da autorização");
define("_MD_LECAT_LANG_AUTH_SETTING","Definição da autorização");
define("_MD_LECAT_LANG_AUTH_TITLE","Título de exibição da autorização");
define("_MD_LECAT_LANG_CAT","Categoria");
define("_MD_LECAT_LANG_CAT_DELETE","Remover categoria");
define("_MD_LECAT_LANG_CAT_EDIT","Editar categoria");
define("_MD_LECAT_LANG_CAT_ID","CAT_ID");
define("_MD_LECAT_LANG_CATEGORY","Categoria");
define("_MD_LECAT_LANG_CONTROL","Controle");
define("_MD_LECAT_LANG_DEFAULT_PERMISSIONS","Conjunto de definições padrão");
define("_MD_LECAT_LANG_DELEET_ALL_PERMIT","Remover todas as permissões desta categoria");
define("_MD_LECAT_LANG_DEPTH","Profundidade");
define("_MD_LECAT_LANG_DESCRIPTION","Descrição");
define("_MD_LECAT_LANG_EDIT_ACTOR","Editar atores");
define("_MD_LECAT_LANG_GROUPID","Número do grupo");
define("_MD_LECAT_LANG_LEVEL","Profundidade máxima");
define("_MD_LECAT_LANG_LEVEL_UNLIMITED","Profundidade ilimitada");
define("_MD_LECAT_LANG_MANAGER","Gerente");
define("_MD_LECAT_LANG_MODULES","Módulos");
define("_MD_LECAT_LANG_MODULES_CONFINEMENT","Confinamento de módulo");
define("_MD_LECAT_LANG_OPTIONS","Opção");
define("_MD_LECAT_LANG_P_ID","Número da categoria anterior");
define("_MD_LECAT_LANG_PARENT","Categoria secundária");
define("_MD_LECAT_LANG_PERMISSION_ON","Permitido");
define("_MD_LECAT_LANG_PERMISSION_TYPE","Tipo de permissão");
define("_MD_LECAT_LANG_PERMISSIONS","Permissões");
define("_MD_LECAT_LANG_PERMIT_DELETE","Permitir remoções");
define("_MD_LECAT_LANG_PERMIT_EDIT","Permitir edições");
define("_MD_LECAT_LANG_PERMIT_ID","PERMIT_ID");
define("_MD_LECAT_LANG_POSTER","Autor");
define("_MD_LECAT_LANG_SET","Definições da categoria ");
define("_MD_LECAT_LANG_SET_DELETE","Remover");
define("_MD_LECAT_LANG_SET_EDIT","Editar");
define("_MD_LECAT_LANG_SET_ID","Definir ID");
define("_MD_LECAT_LANG_TITLE","Título");
define("_MD_LECAT_LANG_TOP_CAT","Categoria principal");
define("_MD_LECAT_LANG_UID","UID");
define("_MD_LECAT_LANG_VIEWER","Leitor");
define("_MD_LECAT_LANG_WEIGHT","Peso");
define("_MD_LECAT_MESSAGE_CONFIRM_DELETE","Tem certeza que deseja remover?");
define("_MD_LECAT_MESSAGE_CONFIRM_SET_DELETE","Tem certeza que deseja remover? Todas as categorias pertencentes a esta categoria de jogos são removidos também.");
define("_MD_LECAT_TIPS_CATEGORY_SET","<p>Lecat é um módulo de gerenciamento de categorias.<br />Isto significa que outros módulos (como os de fóruns, notícias, artigos, etc) podem utilizar o Lecat como mecanismo de gestão de suas próprias categorias.<br /></p><p>Lecat fornece duas funções principais a outros módulos:</p><ul><li>Lista de Categorias (como uma árvore)</li><li>Verificação de permissão para cada categoria.</li></ul></p><h3>Conjunto de categorias</h3><p>Cada módulo requer categorias próprias. Por exemplo, um módulo sobre as novidades do portal requer categorias do tipo 'Atualizações', 'Novos associados', 'Novas mensagens'. Por outro lado, um módulo de fórum pode utilizar categorias como 'Perguntas', 'Solicitações', 'Conversas'. <br /> Assim, você pode criar conjuntos de diversas categorias. Para notícias, fóruns, etc. </p><p>Nesse caso, você deverá instalar uma cópia do módulo Lecat e criar um novo conjunto de categorias.</p>");
define("_MD_LECAT_TIPS_LEVEL","Max profundidade na árvore de categorias. Definir como <q>0</q> (zero) significa <q>sem limite</q>.");
define("_MD_LECAT_TIPS_MODULE_CONFINEMENT","Definir o nome do módulo separados por vírgulas se você quiser aplicar esta categoria em módulos específicos somente.");
define("_MD_LECAT_TIPS_PERMISSIONS","Deixe as permissões a seguir se você deseja que esta subcategoria herde as permissões da categoria principal.");
?>