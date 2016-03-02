<?php
// ******************************************************************** //
// ** XOOPS Cube Legacy - AltSys Module - Portuguese
// ** Por Mikhail Miguel <mikhail.miguel@gmail.com> - http://xoops.net.br/
// ** $Id: compilehookadmin.php 1040 2011-11-06 05:24:00Z mikhail $
// **	License http://creativecommons.org/licenses/by/2.5/br/
// ******************************************************************** //
//
define("_TPLSADMIN_CNF_DELETEOK", "Deseja remover?"); // remover o...?
define("_TPLSADMIN_CNF_ENCLOSEBYBORDEREDDIV", "Os caches dos modelos compilados ser�o abertos e fechados por c�digos <q><code>DIV</code></q>. Confirma?");
define("_TPLSADMIN_CNF_ENCLOSEBYCOMMENT", "Os caches dos modelos compilados ser�o delimitadas pelos coment�rios do tplsadmin. Voc� concorda com isso?");
define("_TPLSADMIN_CNF_HOOKSAVEVARS", "Nas compila��es dos caches dos modelos ser� implantada a l�gica para coleta das vari�veis do modelo. Voc� concorda com isso?");
define("_TPLSADMIN_CNF_REMOVEHOOKS", "Voc� concorda com a normaliza��o?");
define("_TPLSADMIN_DD_ENCLOSEBYBORDEREDDIV", "Cada modelo ser� envolto por c�digos <q><code>DIV</code></q> de bordas pretas. Um link para controle da edi��o do tplsadmin ser� inserido em cada um dos modelos. Embora isso muitas vezes cause a destrui��o do design, voc� pode editar cada modelo mais instintiva e facilmente.");
define("_TPLSADMIN_DD_ENCLOSEBYCOMMENT", "Dois coment�rios em HTML ser�o colocados nos pontos de come�o e final de cada modelo. Uma vez que isso raramente quebra os design, isso � recomendado para profissionais que podem eles mesmos ler HTML");
define("_TPLSADMIN_DD_GETTEMPLATES", "Selecione um conjunto antes de apertar cada bot�o.");
define("_TPLSADMIN_DD_GETTPLSVARSINFO_DW", "Primeiro , abra o Gerenciador de Extens�es do Adobe DreamWeaver.<br />Extraia o arquivo descarregado.<br />Execute os arquivos com a extens�o .mxi e voc� encontrar� di�logos de instala��o.<br />Os snippets para vari�veis de modelo de seu portal ser�o utiliz�veis ap�s reiniciar o Adobe DreamWeaver."); // muito confuso... snippets?
define("_TPLSADMIN_DD_HOOKSAVEVARS", "O primeiro passo para obten��o das informa��es das vari�veis de modelo em seu site. As informa��es das vari�veis do modelo ser�o coletadas quando o lado p�blico de seu site for exibido. Ap�s os modelos que voc� deseja editar forem mostrados, obtenha as informa��es das vari�veis do modelo atrav�s dos bot�es subjacentes.");
define("_TPLSADMIN_DD_PUTTEMPLATES", "Selecione um conjunto que voc� queira enviar ou subescrever, antes de enviar o arquivo zip ou tgz incluindo esses arquivos des modelos (.html). Voc� n�o precisa verificar a profundidade dos caminhos nos arquivos.");
define("_TPLSADMIN_DD_REMOVEHOOKS", "Isso remove coment�rios, c�digos <q><code>DIV</code></q>, e sequ�ncias l�gicas inseridas pelas opera��es acima em cada cache compilado do modelo.");
define("_TPLSADMIN_DT_ENCLOSEBYBORDEREDDIV", "Inserir c�digos <q><code>DIV</code></q>"); // N�o tenho certeza pois ainda n�o trabalhei direito com esses recursos...
define("_TPLSADMIN_DT_ENCLOSEBYCOMMENT", "Inserir coment�rios"); // N�o tenho certeza pois ainda n�o trabalhei direito com esses recursos...
define("_TPLSADMIN_DT_GETTEMPLATES", "Descarregar os modelos"); // Prefiro traduzir download como descarregar. Pode haver um estranhamento inicial, mas vale a pena
define("_TPLSADMIN_DT_GETTPLSVARSINFO_DW", "Obter informa��es das vari�veis de modelo como extens�es do Adobe DreamWeaver");
define("_TPLSADMIN_DT_HOOKSAVEVARS", "Inserir sequ�ncias l�gicas para coletar as vari�veis do modelo"); // N�o tenho certeza...
define("_TPLSADMIN_DT_PUTTEMPLATES", "Enviar os modelos"); // Enviar para...
define("_TPLSADMIN_DT_REMOVEHOOKS", "Normalizar compila��o dos caches dos modelos"); // Como assim?
define("_TPLSADMIN_ERR_EXTENSION", "Esta extens�o n�o p�de ser reconhecida."); // obviamente o acento diferencial continua para P�de e Pode
define("_TPLSADMIN_ERR_INVALIDARCHIVE", "O arquivo n�o p�de ser descompactado."); // v�lido apenas para arquivos compactados?
define("_TPLSADMIN_ERR_INVALIDTPLSET", "O nome escolhido para o conjunto de modelos foi considerado inv�lido pelo sistema."); // ficou natural, mas bem longo
define("_TPLSADMIN_ERR_NOTPLSVARSINFO", "N�o h� arquivos com informa��es sobre as vari�veis do modelo.");
define("_TPLSADMIN_ERR_NOTUPLOADED", "Os arquivos n�o foram enviados."); // ent�o ocorreu um erro?
define("_TPLSADMIN_FMT_MSG_ENCLOSEBYBORDEREDDIV", "%d os caches dos modelos est�o sendo envoltos por c�digos <q><code>DIV</code></q>"); // caches???
define("_TPLSADMIN_FMT_MSG_ENCLOSEBYCOMMENT", "%d os caches dos modelos foram delimitados pelos coment�rios do tplsadmin"); // caches???
define("_TPLSADMIN_FMT_MSG_HOOKSAVEVARS", "%d nos caches dos modelos est�o sendo inseridas sequ�ncias l�gicas para coletar as vari�veis do modelo");
define("_TPLSADMIN_FMT_MSG_PUTTEMPLATES", "%d modelos foram importados."); // importados para onde?
define("_TPLSADMIN_FMT_MSG_REMOVEHOOKS", "%d os caches de modelo foram normalizados");
define("_TPLSADMIN_MSG_CLEARCACHE", "Os caches dos modelos foram removidos"); // n�o gostei de 'CACHES'... vou tentar melhorar
define("_TPLSADMIN_MSG_CREATECOMPILECACHEFIRST", "Ainda n�o foi criado qualquer cache de modelos compilados. O primeiro passo para criar os arquivos de cache � tornar o seu portal acess�vel ao p�blico.");
define("_TPLSADMIN_NUMCAP_COMPILEDCACHES", "Caches de modelos compilados"); // cacheS???
define("_TPLSADMIN_NUMCAP_TPLSVARS", "Arquivos com informa��es sobre as vari�veis do modelo"); // n�o sei se � isso o que o GiJOE quis dizer. Ele � um g�nio, mas o ingl�s dele consegue ser pior que o meu... :-)
;
