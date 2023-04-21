<?php
/**
 * @author Nuno Luciano aka gigamaster XCL23/PHP7
 * @author Mikhail Miguel <mikhail.miguel@gmail.com> - 2011-11-06 05:24:00Z
 * @license http://creativecommons.org/licenses/by/2.5/br/
 */

define( '_TPLSADMIN_INTRO', 'Apresentando um gancho para compilar modelos');

define( '_TPLSADMIN_DESC', 'Ganchos de compilação fornecem uma maneira fácil de inserir auxiliares de edição visual em seus modelos e coletar variáveis ​​do Smarty.
Essas funções estão disponíveis apenas em modelos de front-end e módulos duplicados para os quais foram escritas. ');

define( '_TPLSADMIN_NOTE', 'Importante : Embora a ajuda visual tenha como objetivo destacar a estrutura de seu layout e modelos, existem limitações ao reconhecimento baseado em características, por exemplo, de componentes e modelos personalizados! ');

define( '_TPLSADMIN_TASK_Title', 'Por que e quando realizar esta tarefa!');
define( '_TPLSADMIN_TASK', '
Você pode usar os modelos compilados para concluir as seguintes tarefas:<br>
<ul>
<li>visão geral estrutural que facilita o reconhecimento de falhas de design funcional</li>
<li>inserir elementos de sobreposição que são renderizados para cada componente e modelo incluídos
<li>inserir comentários de código para facilitar a edição do código-fonte</li>
<li>detectar e resolver diferenças entre o design de um modelo e sua implementação.</li>
<li>gera o código do aplicativo usado em modelos e coleta as variáveis do Smarty.</li>
</ul>');

define( '_TPLSADMIN_CACHE_TITLE', 'Modelo compilado');
define( '_TPLSADMIN_CACHE_DESC' , 'O modelo de origem permanece inalterado, na maioria dos casos, você pode excluir todos os arquivos de modelo em cache e executar <b>Normalizar</b>. A compilação gera um novo conjunto de arquivos.' );

define( '_TPLSADMIN_FMT_MSG_ENCLOSEBYCOMMENT' , "%d modelos em cache delimitados com comentários de tplsadmin.");
define( '_TPLSADMIN_DT_ENCLOSEBYCOMMENT' , "Adicione comentários no código fonte");
define( '_TPLSADMIN_DD_ENCLOSEBYCOMMENT' , "Adicione comentários HTML no início e no final de cada modelo. Como isso não afeta o design, é recomendável para modificar o código-fonte.");
define( '_TPLSADMIN_CNF_ENCLOSEBYCOMMENT' , 'Adicionar um comentário "tplsadmin" aos modelos compilados em cache. Confirme para continuar ou cancele!');

define( '_TPLSADMIN_FMT_MSG_ENCLOSEBYBORDEREDDIV' , "%d os caches dos modelos estão sendo envoltos por códigos <q><code>DIV</code></q>");
define( '_TPLSADMIN_DT_ENCLOSEBYBORDEREDDIV' , "Adicione tags div ao redor dos modelos.");
define( '_TPLSADMIN_DD_ENCLOSEBYBORDEREDDIV' , "Cada modelo é envolto por uma tag div e um link para o controlador de edição. Embora isso afete o design geral, você pode identificar facilmente o modelo que deseja editar.");
define( '_TPLSADMIN_CNF_ENCLOSEBYBORDEREDDIV' , "Adicionar tags div ao redor dos modelos.. Confirme para prosseguir ou cancele!");

define( '_TPLSADMIN_FMT_MSG_HOOKSAVEVARS' , "%d nos caches dos modelos estão sendo inseridas sequências lógicas para coletar as variáveis do modelo");
define( '_TPLSADMIN_DD_HOOKSAVEVARS' , "O primeiro passo para obtenção das informações das variáveis de modelo em seu site. As informações das variáveis do modelo serão coletadas quando o lado público de seu site for exibido. Após os modelos que você deseja editar forem mostrados, obtenha as informações das variáveis do modelo através dos botões subjacentes.");
define( '_TPLSADMIN_DT_HOOKSAVEVARS' , "Adicione sequências lógicas para coletar as variáveis do modelo.");
define( '_TPLSADMIN_CNF_HOOKSAVEVARS' , "Adicionar lógica para coletar variáveis de modelo. Confirme para prosseguir ou cancele!");

define( '_TPLSADMIN_FMT_MSG_REMOVEHOOKS' , "%d modelos em cache normalizados.");
define( '_TPLSADMIN_DT_REMOVEHOOKS' , "Normalizar compilação dos modelos em caches.");
define( '_TPLSADMIN_DD_REMOVEHOOKS' , "Isso remove comentários, códigos <q><code>DIV</code></q>, e sequências lógicas inseridas pelas operações acima em cada cache compilado do modelo.");
define( '_TPLSADMIN_CNF_REMOVEHOOKS' , "Confirme para prosseguir ou cancele!");

define( '_TPLSADMIN_MSG_CLEARCACHE' , "Os caches dos modelos foram removidos");
define( '_TPLSADMIN_MSG_CREATECOMPILECACHEFIRST' , "Ainda não foi criado qualquer cache de modelos compilados. O primeiro passo para criar os arquivos de cache é tornar o seu portal acessível ao público.");

define( '_TPLSADMIN_CNF_DELETEOK' , "Deseja remover?");

define( '_TPLSADMIN_DD_GETTPLSVARSINFO_DW' , "Primeiro , abra o Gerenciador de Extensões do Adobe DreamWeaver.<br>Extraia o arquivo descarregado.<br>Execute os arquivos com a extensão .mxi e você encontrará diálogos de instalação.<br>Os snippets para variáveis de modelo de seu site serão utilizáveis após reiniciar o Adobe DreamWeaver.");
define( '_TPLSADMIN_DT_GETTPLSVARSINFO_DW' , "Obter informações das variáveis de modelo como extensões do Adobe Dreamweaver");

define( '_TPLSADMIN_DT_GETTEMPLATES' , "Descarregar os modelos");
define( '_TPLSADMIN_DD_GETTEMPLATES' , "Selecione um conjunto de modelos para baixar e pressione qualquer botão.");

define( '_TPLSADMIN_FMT_MSG_PUTTEMPLATES' , "%d modelos foram importados.");
define( '_TPLSADMIN_DT_PUTTEMPLATES' , "Enviar os modelos");
define( '_TPLSADMIN_DD_PUTTEMPLATES' , "Selecione um conjunto de modelos que você deseja substituir.<br>Selecione o arquivo <b>tar</b> incluindo os modelos (.html)<br>Extrair automaticamente todos os modelos, independentemente da estrutura em árvore do directório do arquivo.");

define( '_TPLSADMIN_ERR_NOTUPLOADED' , "Os arquivos não foram enviados.");
define( '_TPLSADMIN_ERR_EXTENSION' , "Esta extensão não pôde ser reconhecida.");
define( '_TPLSADMIN_ERR_INVALIDARCHIVE' , "O arquivo não pôde ser descompactado.");
define( '_TPLSADMIN_ERR_INVALIDTPLSET' , "O nome escolhido para o conjunto de modelos foi considerado inválido pelo sistema.");
define( '_TPLSADMIN_ERR_NOTPLSVARSINFO' , "Não há arquivos com informações sobre as variáveis do modelo.");

define( '_TPLSADMIN_NUMCAP_COMPILEDCACHES' , "Modelos compilados no diretório de cache");
define( '_TPLSADMIN_NUMCAP_TPLSVARS' , "Modelos compilados com opções de edição");
