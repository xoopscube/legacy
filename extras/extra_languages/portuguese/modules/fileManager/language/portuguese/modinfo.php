<?php
// Translation Info
// $Id$
// License http://creativecommons.org/licenses/by/2.5/br/
// ############################################################### //
// ## XOOPS Cube Legacy 2.1 - Tradução para o Português do Brasil
// ############################################################### //
// ## Por............: Mikhail Miguel
// ## E-mail.........: mikhail@underpop.com
// ## Website........: http://xoopscube.com.br
// ############################################################### //
// *************************************************************** //
/**
 * Filemaneger
 * (C)2007-2009 BeaBo Japan by Hiroki Seike
 * http://beabo.net/
 **/

define("_MI_FILEMANAGER_NAME","Arquivos");
define("_MI_FILEMANAGER_DESC","Gerenciador de pastas e arquivos, com opção de manipulação em lote.");
define("_MI_FILEMANAGER_UPDATE","Atualizar");

// --------------------------------------------------------
// Names of admin menu items
// --------------------------------------------------------
define("_MI_FILEMANAGER_MAIN","Lista de arquivos");
define("_MI_FILEMANAGER_MAIN_DSC","lista de arquivos com miniaturas visualizador de imagens");
define("_MI_FILEMANAGER_UPLOAD","Enviar arquivo");
define("_MI_FILEMANAGER_UPLOAD_DSC","Enviar arquivos para o diretório <q>uploads</q>");

define("_MI_FILEMANAGER_FOLDER","Pasta");
define("_MI_FILEMANAGER_FOLDER_DSC","Gestão de diretórios e suas permissões");

define("_MI_FILEMANAGER_CHECK","Verificar configuração");
define("_MI_FILEMANAGER_CHECK_DSC","Verifique as configurações do gerenciador de arquivos");

// --------------------------------------------------------
// PreferenceEdit
// --------------------------------------------------------
define("_MI_FILEMANAGER_PATH","Principal diretório para envios");
define("_MI_FILEMANAGER_PATH_DSC","Defina o caminho sob /uploads/ (nome da pasta apenas, sem a barra no fim)");
define("_MI_FILEMANAGER_DIRHANDLE","Gestor de diretórios");
define("_MI_FILEMANAGER_DIRHANDLE_DSC","Permitir ou não webmasters para criar e apagar pastas");
define("_MI_FILEMANAGER_THUMBSIZE","Tamanho da miniatura");
define("_MI_FILEMANAGER_THUMBSIZE_DSC","Especifique a largura máxima das miniaturas para a lista de arquivos");
define("_MI_FILEMANAGER_DEBUGON","Habilitar o modo de depuração do gerenciador");
define("_MI_FILEMANAGER_DEBUGON_DSC","Ativar ou não console debug do uploader que é prestado dentro de um iframe");


define("_MI_FILEMANAGER_XOOPSLOCK","Ocultar imagens do sistema?");
define("_MI_FILEMANAGER_XOOPSLOCK_DSC","Mostra ou não arquivos de 'Image Manager' (por exemplo:. avatares, ícones emoticos, etc)");
define("_MI_FILEMANAGER_EXTENSIONS","Extensões dos arquivos permitidos para serem enviados ao servidor");
define("_MI_FILEMANAGER_EXTENSIONS_DSC","Separe as extensões dos formatos dos arquivos com barras verticais.<br /> Certifique-se de utilizar apenas minúsculas e não incluir espações entre elas as extensões. <br />O valor predefinido é: <q>gif|jpg|jpeg|png|avi|mov|wmv|mp3|mp4|flv|doc|xls|ods|odt|pdf</q>");

// reserved  options setting

define("_MI_FILEMANAGER_FUSE","[ffmpeg] Utilizar FFmpeg");
define("_MI_FILEMANAGER_FUSE_DSC","FFmpeg é uma solução multi-plataforma completa para gravar, converter e servir conteúdo  em áudio e vídeo. <br /> FFmpeg deve ser suportada pelo servidor. Se não estiver, instale o FFmpeg binários para o seu servidor");
define("_MI_FILEMANAGER_FPATH","[ffmpeg] Caminho do FFmpeg");
define("_MI_FILEMANAGER_FPATH_DSC","Especifique o caminho de instalação FFmpeg. <br /> (Exemplo: <tt> / usr / local / bin </ tt>, <tt> / usr / bin </ tt >)");
define("_MI_FILEMANAGER_FOPT","[ffmpeg] Opção");
define("_MI_FILEMANAGER_FOPT_DSC","Especifique o comando opção. Sua versão não está disponível");
define("_MI_FILEMANAGER_FCAPTURE","[ffmpeg] tempo para o Screen Shot ");
define("_MI_FILEMANAGER_FCAPTURE_DSC","Tempo a partir do início do vídeo para ter uma imagem");
define("_MI_FILEMANAGER_FCONVERT","[ffmpeg] tamanho máximo de FLV conversão");
define("_MI_FILEMANAGER_FCONVERT_DSC","Especifique o tamanho máximo de arquivos FLV vídeo a ser convertido para o formato video. A unidade é MB");
define("_MI_FILEMANAGER_FMOVIEFILE","[ffmpeg] Formato do arquivo para converter para FLV");
define("_MI_FILEMANAGER_FMOVIEFILE_DSC","Separe as extensões dos formatos dos arquivos com barras verticais.<br />Certifique-se de utilizar todas as palavras em minúsculas e de não incluir espaços entre elas. <br /> O valor predefinido é <q>flv|avi|mwv|mov|mpg|qt|mov|3gp|3gp2|mp4</q>");
?>