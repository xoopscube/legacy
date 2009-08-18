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

// --------------------------------------------------------
// Main
// --------------------------------------------------------
define('_AD_FILEMANAGER_MAIN_DSC', "Lista dos arquivos enviados. Quando você clicar sobre uma pasta, você se moverá para a pasta. A vizualização da imagem grande e do vídeo está disponível.");
define('_AD_FILEMANAGER_PATH_HOME', "Início");
define('_AD_FILEMANAGER_TYPE', "Tipo");
define('_AD_FILEMANAGER_EDIT', "Adicionar");
define('_AD_FILEMANAGER_DEL', "Excluir");
define('_AD_FILEMANAGER_PARENT', "Retornar");
define('_AD_FILEMANAGER_RETURN', "Retornar para a lista");
define('_AD_FILEMANAGER_ACTION_DELETE', "&nbsp;Excluir&nbsp;");
define('_AD_FILEMANAGER_ACTION_DEFULT', "&nbsp;Enviar&nbsp; ");
define('_AD_FILEMANAGER_ACTION_SUBMIT', "&nbsp;Enviar&nbsp;");
define('_AD_FILEMANAGER_FILE_TOTAL', "Total");

// --------------------------------------------------------
// Error
// --------------------------------------------------------
define('_AD_FILEMANAGER_ERROR_REQUIRED', "{0} é requerido.");
define('_AD_FILEMANAGER_ERROR_PERMISSION', "Não existem permissões de acesso.");
define('_AD_FILEMANAGER_ERROR_DELETE_FOR_PERMISSION', "Você não tem acesso %s para o administrador de arquivo não pode ser removido de.");
define('_AD_FILEMANAGER_NOTFOUND', "Arquivo não encontrado.");

// --------------------------------------------------------
// Uploads
// --------------------------------------------------------
define('_AD_FILEMANAGER_PREVIEW', "Visualização");
define('_AD_FILEMANAGER_FILENAME', "Arquivo");
define('_AD_FILEMANAGER_SIZE', "Tamanho");
define('_AD_FILEMANAGER_DATE', "Data da atualização");
define('_AD_FILEMANAGER_UPLOAD', "Enviar");
define('_AD_FILEMANAGER_UPLOAD_DSC', "Clique em Enviar para selecionar os arquivos em seu computador e começar o envio.");
define('_AD_FILEMANAGER_UPLOAD_NOTACCESS',  "%s não é enviado. Utilizando o software de FTP, por favor altere as permissões.");
define('_AD_FILEMANAGER_NOTFOUNDURL', "O percurso do envio não foi encontrado");
define('_AD_FILEMANAGER_CONFIRMMSSAGE', "Você pode enviar no máximo %s .");
define('_AD_FILEMANAGER_FOLDER_ADD', "Adicionar pasta");

// --------------------------------------------------------
// Folder
// --------------------------------------------------------
define('_AD_FILEMANAGER_FOLDER', "Pasta");
define('_AD_FILEMANAGER_FOLDERNAME', "Novo nome de pasta");
define('_AD_FILEMANAGER_ERROR_FOLDERNAME', "O nome da pasta não está correto.<br />Caracteres alfanumérios podem ser usados no nome da pasta -_.");
define('_AD_FILEMANAGER_ERROR_PATH', "O nome antigo não está correto. Por favor, confirme o nome da pasta.");
define('_AD_FILEMANAGER_ADD', "Adicionar pasta");
define('_AD_FILEMANAGER_ADDFOLDER', "Adicionar pasta");
define('_AD_FILEMANAGER_ADDFOLDER_DSC', "Criar uma nova pasta abaixo do diretório uploads do XOOPS.");
define('_AD_FILEMANAGER_ADDFOLDER_SUCCESS', "Adicionar pasta.");
define('_AD_FILEMANAGER_ADDFOLDER_ERROR', "Não foi possível adicionar uma pasta.");
define('_AD_FILEMANAGER_ADDFOLDER_CONFIRMMSSAGE', "Criar uma pasta abaixo do %s <br />Adicione um nome para a nova pasta.");
define('_AD_FILEMANAGER_DELET', "Excluir");
define('_AD_FILEMANAGER_DELFOLDER', "Excluir pasta");
define('_AD_FILEMANAGER_DELFOLDER_DSC', "Remover a pasta especificada.");
define('_AD_FILEMANAGER_DELFOLDER_CONFIRMMSSAGE', "Excluir pasta %s");
define('_AD_FILEMANAGER_DELFOLDER_FILE_EXISTS', "%s para o arquivo da pasta que não pode ser excluída. Para verificar o conteúdo de uma pasta, por favor.");
define('_AD_FILEMANAGER_DELFOLDER_SUCCESS', "Pasta excluída.");
define('_AD_FILEMANAGER_DELFOLDER_ERROR', "Não foi possível excluir a pasta. Foi especificada um pasta vazia ou as permissões não estão corretas.");
define('_AD_FILEMANAGER_DELFOLDER_ISDIR', "A pasta %s não pode ser removida do administrador de arquivos.");
define('_AD_FILEMANAGER_DELFOLDER_NOTACCESS', "A pasta %s não pode ser removida do administrador de arquivos. Por favor, altere as permissões usando seu software de FTP.");
define('_AD_FILEMANAGER_FILECOUNT', "Arquivos");

// --------------------------------------------------------
// Setting check
// --------------------------------------------------------
define('_AD_FILEMANAGER_CHECK_NG', "A biblioteca de arquivos não foi encontrada. Por favor, envie os arquivos.<br />");
define('_AD_FILEMANAGER_CHECK_OK', "A configuração da biblioteca de arquivos foi concluida.");
define('_AD_FILEMANAGER_CHECK', "Verificar configuração");
define('_AD_FILEMANAGER_CHECK_DSC_1', "Usando SWFUpload");
define('_AD_FILEMANAGER_CHECK_DSC_2', "O uso público do SWFUpload para envio não é recomendado.<br />O htaccess configura o local da biblioteca. Por favor, configure com acesso restrito.");
define('_AD_FILEMANAGER_HTACCESS_DSC_1', "Você pode escolher suas configurações clicando no formulário");
define('_AD_FILEMANAGER_HTACCESS_DSC_2', "Isto permite acessar seu atual endereço de IP.(Sample composição é automático. Por favor, mude para ajustar ao ambiente de seu servidor.)");
define('_AD_FILEMANAGER_HTACCESS_PATH', "Percurso do arquivo htaccess para intalalar");

// --------------------------------------------------------
// SWFUpload
// --------------------------------------------------------
define('_AD_FILEMANAGER_SWF_UPLOAD_QUEUE', "Enviados");
define('_AD_FILEMANAGER_SWF_UPLOAD_CNACEL', "Cancelar todos os enviados");
define('_AD_FILEMANAGER_SWF_COULD_NOT_LOAD', "Lamentamos mas o SWFUpload não pode baixar. Você deve ter o JavaScript habilitado para usufruir o SWFUpload.");
define('_AD_FILEMANAGER_SWF_LOADING', "O SWFUpload está sendo baixado. Por favor, aguarde um momento...");
define('_AD_FILEMANAGER_SWF_LOAD_HAS_FAILED', "O SWFUpload está levando um longo tempo para baixar ou o download falhou. Por favor, certifique-se que o plugin do Flash está habilitado e que uma versão do Adobe Flash Player está intalada e trabalhando.");
define('_AD_FILEMANAGER_SWF_INSTALL_FLASH', "Lamentamos mas o SWFUpload não pode baixar. Você precisa instalar ou atualizar o Flash Player.<br />
Visite o <a href='http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash'>site da Adobe</a> para obter o Flash Player.");

// --------------------------------------------------------
// FFMPEG
// --------------------------------------------------------
define('_AD_FILEMANAGER_ACTION_CONVERT', "Converter para FLV");
define('_AD_FILEMANAGER_ACTION_CAPTURE', "Fazer a caputura da imagem");

?>
