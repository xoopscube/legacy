<?php
/**
 * Filemaneger
 * (C)2007-2009 BeaBo Japan by Hiroki Seike
 * http://beabo.net/
 **/
 /**
 * Translate do Portugues from Brazil
 * by Miraldo Antoninho Ohse (leco)
 * http://mixmusicas.com/
 * mail to: m_ohse@hotmail.com
 * Thanks
 **/

define('_MI_FILEMANAGER_NAME', "Administrador de Arquivos");
define('_MI_FILEMANAGER_DESC', "Administração do arquivo e da pasta com os arquivos enviados.");
define('_MI_FILEMANAGER_UPDATE', 'Atualização');

// --------------------------------------------------------
// Names of admin menu items
// --------------------------------------------------------
define('_MI_FILEMANAGER_MAIN', "Lista de arquivos");
define('_MI_FILEMANAGER_MAIN_DSC', "Lista de arquivos com miniaturas e vizualizações da imagem");
define('_MI_FILEMANAGER_UPLOAD', "Enviar arquivo");
define('_MI_FILEMANAGER_UPLOAD_DSC', "Enviar arquivos para o diretório de envio");

define('_MI_FILEMANAGER_FOLDER', "Pasta");
define('_MI_FILEMANAGER_FOLDER_DSC', "Administração do diretório e permissões");

define('_MI_FILEMANAGER_CHECK', "Verificar configurações");
define('_MI_FILEMANAGER_CHECK_DSC', "Verificar configuraçções no administrador de arquivos.");

// --------------------------------------------------------
// PreferenceEdit
// --------------------------------------------------------
define('_MI_FILEMANAGER_PATH',"Diretório principal para envio");
define('_MI_FILEMANAGER_PATH_DSC',"Definir o percurso de envio abaixo do /uploads/ (somente o nome da pasta, sem a barra '/' )");
define('_MI_FILEMANAGER_DIRHANDLE',"Administração do diretório de envio");
define('_MI_FILEMANAGER_DIRHANDLE_DSC',"Permite ou não o webmasters criar e excluir pastas.");
define('_MI_FILEMANAGER_THUMBSIZE',"Tanmanho das miniaturas");
define('_MI_FILEMANAGER_THUMBSIZE_DSC',"Especificar a largura máxima das miniaturas na lista de arquivos.");
define('_MI_FILEMANAGER_DEBUGON',"Ligar a depuração do envio");
define('_MI_FILEMANAGER_DEBUGON_DSC',"Habilitar ou não o console de depuração de envio que está inserido dentro de uma iframe.");


define('_MI_FILEMANAGER_XOOPSLOCK',"Esconder as imagens do sistema?");
define('_MI_FILEMANAGER_XOOPSLOCK_DSC',"Mostar ou não os arquivos do 'Administrador de Arquivos' (Exemplo: avatars, smilies, etc");
define('_MI_FILEMANAGER_EXTENSIONS',"Extensão dos arquivos permitidos para envio");
define('_MI_FILEMANAGER_EXTENSIONS_DSC',"Separar a extensão dos arquivos com uma barra '|'.<br />Certifique-se de que você está usando todos os caracteres minúsculos para as extensões dos arquivos.<br />A configuração padrão é gif|jpg|jpeg|png|avi|mov|wmv|mp3|mp4|flv|doc|xls|ods|odt|pdf");

// reserved  options setting 

define('_MI_FILEMANAGER_FUSE',"[ffmpeg] Usar FFmpeg");
define('_MI_FILEMANAGER_FUSE_DSC',"o FFmpeg é uma solução de plataforma completa para gravar, converter e transmitir audio e video.<br /> O FFmpeg deve ser suportado pelo servidor. Caso contrário, instale o FFmpeg binário em seu servidor.");
define('_MI_FILEMANAGER_FPATH',"[ffmpeg] percurso para o FFmpeg");
define('_MI_FILEMANAGER_FPATH_DSC',"Especificar o percurso da intalação do FFmpeg.<br />(Examplo:<tt>/usr/local/bin</tt>, <tt>/usr/bin</tt>)");
define('_MI_FILEMANAGER_FOPT',"[ffmpeg] Opções");
define('_MI_FILEMANAGER_FOPT_DSC',"Por favor, especifique a opção de comenado. ãƒ»ãƒ»Sua versão não está disponível ãƒ»ãƒ»");
define('_MI_FILEMANAGER_FCAPTURE',"[ffmpeg] Tempo Screenshot");
define('_MI_FILEMANAGER_FCAPTURE_DSC',"Tempo do começo do vídeo para tomar um screenshot.");
define('_MI_FILEMANAGER_FCONVERT',"[ffmpeg] Tamanho máximo da conversão FLV");
define('_MI_FILEMANAGER_FCONVERT_DSC',"Especificar o tamanho máximo dos arquivos de vídeo FLV par serem convertidos para o formato video. A unidade é MB.");
define('_MI_FILEMANAGER_FMOVIEFILE',"[ffmpeg] Formato dos arquivos para converter para FLV");
define('_MI_FILEMANAGER_FMOVIEFILE_DSC',"Separar o formado das extensões dos arquivos com uma barra '|'.<br />Certifique-se de que você está usando todos os caracteres minúsculos para as extensões dos arquivos.<br />O formato do arquivo é convertido na configuração do ffmpeg.<br />A configuração padrão é flv|avi|mwv|mov|mpg|qt|mov|3gp|3gp2|mp4");

?>
