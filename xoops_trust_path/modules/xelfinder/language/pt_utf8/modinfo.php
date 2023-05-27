<?php

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) {
	$mydirname = 'xelfinder';
}
$constpref = '_MI_' . strtoupper( $mydirname );

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref . '_LOADED' ) ) {

// a flag for this language file has already been read or not.
	define( $constpref . '_LOADED', 1 );

	define( $constpref . '_DESC', 'The module which uses the file manager elFinder of a Web base as an image manager.' );

// admin menu
define( $constpref.'_ADMENU_INDEX_CHECK' , 'Configuração' ) ;
define( $constpref.'_ADMENU_GOTO_MODULE' , 'Ver o módulo' ) ;
define( $constpref.'_ADMENU_GOTO_MANAGER' ,'Gerenciador' ) ;
define( $constpref.'_ADMENU_DROPBOX' ,     'Token do Dropbox' ) ;
define( $constpref.'_ADMENU_GOOGLEDRIVE' , 'API do Google Drive' ) ;
define( $constpref.'_ADMENU_VENDORUPDATE' ,'Atualizar "vendor"' ) ;
define( $constpref.'_ADMENU_MYLANGADMIN',  'Idioma');
define( $constpref.'_ADMENU_MYTPLSADMIN',  'Templates');
define( $constpref.'_ADMENU_MYBLOCKSADMIN','Permissões');
define( $constpref.'_ADMENU_MYPREFERENCES','Preferências');

// configurations
    define( $constpref.'_MANAGER_TITLE' ,           'Título da página do Gerenciador' );
    define( $constpref.'_MANAGER_TITLE_DESC' ,      '' );
    define( $constpref.'_VOLUME_SETTING' ,          'Drivers de volume' );
    define( $constpref.'_VOLUME_SETTING_DESC' ,     '<button class="help-admin button" type="button" data-module="xelfinder" data-help-article="#help-volume" title="Help Volume"><b>?</b></button> Opções de configuração separadas por uma nova linha' );
    define( $constpref.'_SHARE_FOLDER' ,            'Pasta compartilhada' );
    define( $constpref.'_DISABLED_CMDS_BY_GID' ,    'Configurações de Diretiva de Grupo - Desabilitar comandos' );
    define( $constpref.'_DISABLED_CMDS_BY_GID_DESC','[GroupID]= comandos desabilitados (separados por vírgula ",")<br>Valor padrão: 3=mkdir,paste,archive,extract.<br>Adicionar um novo GroupID e desabilitar comandos com delimitador de dois pontos ":"<br>Lista de comandos: archive, chmod, cut, duplicate, edit, empty, extract, mkdir, mkfile, paste, perm, put, rename, resize, rm, upload etc...' );
    define( $constpref.'_DISABLE_WRITES_GUEST' ,    'Configurações de Diretiva de Grupo - Desabilitar comandos de escrita para usuários anônimos' );
    define( $constpref.'_DISABLE_WRITES_GUEST_DESC','Todos os comandos de escrita estão desabilitados para usuários anônimos.<br>Desabilita escrita e modificação, permitindo a leitura.' );
    define( $constpref.'_DISABLE_WRITES_USER' ,     'Configurações de Diretiva de Grupo - Desabilitar comandos de gravação para usuários' );
    define( $constpref.'_DISABLE_WRITES_USER_DESC', 'Todos os comandos de escrita desabilitados para usuários registrados.' );
    define( $constpref.'_ENABLE_IMAGICK_PS' ,       'Ativar PostScript do ImageMagick' );
    define( $constpref.'_ENABLE_IMAGICK_PS_DESC',   'Se as vulnerabilidades foram corrigidas em <a href="https://www.kb.cert.org/vuls/id/332928" target="_blank" rel="noopener nofollow">Ghostscript ↗ 🌐</a>, você pode ativar o processamento de PostScript com ImageMagick selecionando "Sim".' );
    define( $constpref.'_USE_SHARECAD_PREVIEW' ,    'Ativar visualização do ShareCAD' );
    define( $constpref.'_USE_SHARECAD_PREVIEW_DESC','Use o ShareCAD para expandir a visualização de tipos de arquivos com o serviço gratuito de <a href="https://sharecad.org/de/Home/PrivacyPolicy" target="_blank" rel="noopeneer nofollow">ShareCAD.org [ Política de Privacidade ] ↗ 🌐</a>' );
    define( $constpref.'_USE_GOOGLE_PREVIEW' ,      'Ativar visualização do Google Docs' );
    define( $constpref.'_USE_GOOGLE_PREVIEW_DESC',  'Use o Google Docs para expandir a visualização de tipos de arquivo. Consulte a Política de Privacidade do Google Docs.' );
    define( $constpref.'_USE_OFFICE_PREVIEW' ,      'Ativar a visualização do Office Online' );
    define( $constpref.'_USE_OFFICE_PREVIEW_DESC',  'Observação: a Microsoft não apenas coleta dados do usuário por meio do cliente de telemetria integrado, mas também registra e armazena o uso individual de Connected Services. A URL do conteúdo é coletada por products.office.com' );
    define( $constpref.'_MAIL_NOTIFY_GUEST' ,       'Ativar notificação por e-mail - Usuário anônimo' );
    define( $constpref.'_MAIL_NOTIFY_GUEST_DESC',   'Notifique um administrador de arquivo carregado por usuário anônimo.' );
    define( $constpref.'_MAIL_NOTIFY_GROUP' ,       'Ativar notificação por e-mail - Grupos' );
    define( $constpref.'_MAIL_NOTIFY_GROUP_DESC',   'Notifique um administrador de arquivo carregado por grupos selecionados.' );
    define( $constpref.'_FTP_NAME' ,                'Volume da rede FTP' );
    define( $constpref.'_FTP_NAME_DESC' ,           'Exiba o nome do volume de rede da conexão FTP para administradores.' );
    define( $constpref.'_FTP_HOST' ,                'FTP - nome de anfitrião' );
    define( $constpref.'_FTP_HOST_DESC' ,           '' );
    define( $constpref.'_FTP_PORT' ,                'FTP - Porta. Padrão: 21' );
    define( $constpref.'_FTP_PORT_DESC' ,           '' );
    define( $constpref.'_FTP_PATH' ,                'FTP - caminho do diretório raiz' );
    define( $constpref.'_FTP_PATH_DESC' ,           'Esta configuração também é usada para o driver de volume de plug-in "ftp". Deixe em branco apenas para o plug-in "ftp".' );
    define( $constpref.'_FTP_USER' ,                'FTP - nome de usuário' );
    define( $constpref.'_FTP_USER_DESC' ,           '' );
    define( $constpref.'_FTP_PASS' ,                'FTP - senha' );
    define( $constpref.'_FTP_PASS_DESC' ,           '' );
    define( $constpref.'_FTP_SEARCH' ,              'FTP - integração de volume nos resultados da pesquisa' );
    define( $constpref.'_FTP_SEARCH_DESC' ,         'Alguns firewalls ou roteadores de rede podem desconectar as conexões com erro “Tempo limite de conexão esgotado” se o servidor demorar demais para responder e enviar informações.' );
    define( $constpref.'_BOXAPI_ID' ,               'Box - API OAuth2 client_id' );
    define( $constpref.'_BOXAPI_ID_DESC' ,          'Se registrar no <a href="https://app.box.com/developers/services" target="_blank" rel="noopeneer nofollow">Box API Console ↗ 🌐</a>' );
    define( $constpref.'_BOXAPI_SECRET' ,           'Box - API OAuth2 client_secret' );
    define( $constpref.'_BOXAPI_SECRET_DESC' ,      'Para usar o Box como um volume de rede, defina o redirect_url na seção de configuração do aplicativo Box API :<br><small><pre>'.str_replace('http://','https://',XOOPS_URL).'/modules/'.$mydirname.'/connector.php</pre></small><br>HTTPS is required. Optional paths after domain can be omitted.' );
    define( $constpref.'_GOOGLEAPI_ID' ,            'Google API - Client ID' );
    define( $constpref.'_GOOGLEAPI_ID_DESC' ,       'Se registrar no <a href="https://console.developers.google.com" target="_blank" rel="noopeneer nofollow">Google API Console ↗ 🌐</a>' );
    define( $constpref.'_GOOGLEAPI_SECRET' ,        'Google API - Client Secret' );
    define( $constpref.'_GOOGLEAPI_SECRET_DESC' ,   'Para usar o Google Drive como um volume de rede, defina redirect_uri no Google Developer Console :<br><small><pre>'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php?cmd=netmount&protocol=googledrive&host=1</pre></small>' );
    define( $constpref.'_ONEDRIVEAPI_ID' ,          'OneDrive - API Application ID' );
    define( $constpref.'_ONEDRIVEAPI_ID_DESC' ,     'Se registrar no <a href="https://portal.azure.com/#blade/Microsoft_AAD_IAM/ActiveDirectoryMenuBlade/RegisteredApps" target="_blank" rel="noopeneer nofollow">Azure Active Directory Registered Apps ↗ 🌐</a>' );
    define( $constpref.'_ONEDRIVEAPI_SECRET' ,      'OneDrive - API Password' );
    define( $constpref.'_ONEDRIVEAPI_SECRET_DESC' , 'Para usar o OneDrive como um volume de rede, adicione esta URL de redirecionamento nas configurações do aplicativo OneDrive API :<br><small><pre>'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php/netmount/onedrive/1</pre></small>' );
    define( $constpref.'_DROPBOX_TOKEN' ,           'Dropbox.com - App key' );
    define( $constpref.'_DROPBOX_TOKEN_DESC' ,      'Se registrar no <a href="https://www.dropbox.com/developers" target="_blank" rel="noopeneer nofollow">Dropbox Developers ↗ 🌐</a>' );
    define( $constpref.'_DROPBOX_SECKEY' ,          'Dropbox.com - App secret' );
    define( $constpref.'_DROPBOX_SECKEY_DESC' ,     'O segredo do aplicativo encontrado na página de configurações do seu aplicativo Dropbox. OAuth 2 URIs de redirecionamento :<br><small><pre>'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php?cmd=netmount&protocol=dropbox2&host=1</pre></small>' );
    define( $constpref.'_DROPBOX_ACC_TOKEN' ,       'Dropbox.com - App secret token' );
    define( $constpref.'_DROPBOX_ACC_TOKEN_DESC' ,  'O token de acesso gerado para o volume compartilhado do Dropbox.<br>Se registrar no <a href="https://www.dropbox.com/developers/apps" target="_blank" rel="noopeneer nofollow">Dropbox.com Developers-Apps ↗ 🌐</a>' );
    define( $constpref.'_DROPBOX_ACC_SECKEY' ,      'Dropxbox.com - OAuth 1 only [ blank for OAuth2 ]' );
    define( $constpref.'_DROPBOX_ACC_SECKEY_DESC' , 'Migrar tokens de acesso ou autenticar novamente com uma nova API de permissão v1 → v2<br>Deixe este campo vazio e use a nova chave de aplicativo API v2.' );
    define( $constpref.'_DROPBOX_NAME' ,            'Dropbox.com - Nome do volume compartilhado' );
    define( $constpref.'_DROPBOX_NAME_DESC' ,       'Ao contrário da montagem do volume de rede, o nome do volume compartilhado está disponível para todos os usuários.' );
    define( $constpref.'_DROPBOX_PATH' ,            'Dropxbox.com - caminho raiz do volume compartilhado' );
    define( $constpref.'_DROPBOX_PATH_DESC' ,       'O caminho do volume compartilhado do Dropbox. Exemplo: "/Public"<br>Isso também é usado pelo driver de volume do plug-in do Dropbox.<br>Se você configurar o plug-in "dropbox", deixe este caminho raiz em branco.' );
    define( $constpref.'_DROPBOX_HIDDEN_EXT' ,      'Dropxbox.com - Arquivos ocultos de volume compartilhado' );
    define( $constpref.'_DROPBOX_HIDDEN_EXT_DESC' , 'Arquivos ocultos são exibidos apenas para administradores. Especifique o nome dos arquivos separados por vírgula ",".<br>Isso tem como alvo uma pasta que termina com barra "/"' );
    define( $constpref.'_DROPBOX_WRITABLE_GROUPS' , 'Dropxbox.com - Grupos com acesso total' );
    define( $constpref.'_DROPBOX_WRITABLE_GROUPS_DESC' , 'Qualquer membro do grupo pode adicionar, editar, excluir, compartilhar ou baixar arquivos e pastas. Outros grupos só podem ler.<br>Você pode organizar os membros em grupos. Compartilhe uma pasta ou arquivo com um grupo para conceder acesso automaticamente a todos os membros do grupo.' );
    define( $constpref.'_DROPBOX_UPLOAD_MIME' ,     'Dropxbox.com - Tipo MIME que pode ser carregado no volume compartilhado') ;
    define( $constpref.'_DROPBOX_UPLOAD_MIME_DESC' ,'Tipo MIME que o grupo com permissões de gravação pode carregar. Os administradores não são restritos.') ;
    define( $constpref.'_DROPBOX_WRITE_EXT' ,       'Arquivos compartilhados com permissões de gravação') ;
    define( $constpref.'_DROPBOX_WRITE_EXT_DESC' ,  'As permissões de arquivo são herdadas do grupo com permissões de gravação. Nome do arquivo separado por vírgula ",".<br>Destina-se a uma pasta que termina com barra "/".<br>Os administradores não são restritos.') ;
    define( $constpref.'_DROPBOX_UNLOCK_EXT' ,      'Dropxbox.com - Arquivos desbloqueados compartilhados') ;
    define( $constpref.'_DROPBOX_UNLOCK_EXT_DESC' , 'Arquivos desbloqueados podem ser excluídos, movidos e renomeados.<br>Nome do arquivo separado por vírgula ",".<br>Destina-se a uma pasta que termina com barra "/".<br>Todos os arquivos são desbloqueados para administradores.') ;
    define( $constpref.'_JQUERY' ,                  'URL do jQuery JavaScript' );
    define( $constpref.'_JQUERY_DESC' ,             'CDN ou URL local (versão auto-hospedada recomendada)' );
    define( $constpref.'_JQUERY_UI' ,               'URL do jQuery-UI JavaScript' );
    define( $constpref.'_JQUERY_UI_DESC' ,          'CDN ou URL local (versão auto-hospedada recomendada)' );
    define( $constpref.'_JQUERY_UI_CSS' ,           'URL do jQuery-UI estilo CSS' );
    define( $constpref.'_JQUERY_UI_CSS_DESC' ,      'CDN or local URL (versão auto-hospedada recomendada)' );
    define( $constpref.'_JQUERY_UI_THEME' ,         'Tema jQuery-UI' );
    define( $constpref.'_JQUERY_UI_THEME_DESC' ,    'CDN ou URL local do Tema CSS padrão. Exemplo: smoothness' );
    define( $constpref.'_GMAPS_APIKEY' ,            'Google Maps - Chave de API' );
    define( $constpref.'_GMAPS_APIKEY_DESC' ,       'Google Maps - Chave de API usada na visualização KML' );
    define( $constpref.'_ZOHO_APIKEY' ,             'Zoho office - chave de API do editor' );
    define( $constpref.'_ZOHO_APIKEY_DESC' ,        'Você pode obter a chave da API em <a href="https://www.zoho.com/docs/help/office-apis.html#get-started" target="_blank" rel="noopeneer nofollow">Zoho.com office apis ↗ 🌐</a>' );
    define( $constpref.'_CREATIVE_CLOUD_APIKEY' ,   'Creative Cloud - Chave de API do SDK' );
    define( $constpref.'_CREATIVE_CLOUD_APIKEY_DESC','Você pode obter a chave da API em <a href="https://console.adobe.io/" target="_blank" rel="noopeneer nofollow">Console Adobe ↗ 🌐</a>' );
    define( $constpref.'_ONLINE_CONVERT_APIKEY' ,   'Online-convert.com - Chave API' );
    define( $constpref.'_ONLINE_CONVERT_APIKEY_DESC','Você pode obter a chave da API em <a href="https://apiv2.online-convert.com/docs/getting_started/api_key.html" target="_blank" rel="noopeneer nofollow">Online-convert API ↗ 🌐</a>' );
    define( $constpref.'_EDITORS_JS',               'URL de editors.js' );
    define( $constpref.'_EDITORS_JS_DESC',          'Especifique a URL do JavaScript para personalizar os editores "common/elfinder/js/extras/editors.default.js"' );
    define( $constpref.'_UI_OPTIONS_JS',            'URL de xelfinderUiOptions.js' );
    define( $constpref.'_UI_OPTIONS_JS_DESC',       'Especifique a URL do JavaScript para personalizar "modules/'.$mydirname.'/include/js/xelfinderUiOptions.default.js"' );
    define( $constpref.'_THUMBNAIL_SIZE' ,          'Tamanho da imagem miniatura de inserção' );
    define( $constpref.'_THUMBNAIL_SIZE_DESC' ,     'O valor padrão em pixels do tamanho da miniatura a ser inserido no BBcode.' );
    define( $constpref.'_DEFAULT_ITEM_PERM' ,       'Definir permissões para novos itens' );
    define( $constpref.'_DEFAULT_ITEM_PERM_DESC' ,  'As permissões são hexadecimais de três dígitos [proprietário][grupo][outros]<br>Número binário de 4 bits, cada dígito é para [Ocultar][Ler][Escrever][Desbloquear]<br>744 Owner: 7 =-rwu, group 4 =-r--, Guest 4 =-r--' );
    define( $constpref.'_USE_USERS_DIR' ,           'Ativar pasta para cada usuário' );
    define( $constpref.'_USE_USERS_DIR_DESC' ,      '' );
    define( $constpref.'_USERS_DIR_PERM' ,          'Definir permissão de "Ativar pasta para cada usuário"' );
    define( $constpref.'_USERS_DIR_PERM_DESC' ,     'Este parâmetro é apenas uma referência quando um item é criado. Altere as permissões diretamente no elFinder.<br>ex. 7cc: Owner 7 = -rwu, Group c = hr--, Guest c = hr--' );
    define( $constpref.'_USERS_DIR_ITEM_PERM' ,     'Definir permissão de novos itens em "Ativar pasta para cada usuário"' );
    define( $constpref.'_USERS_DIR_ITEM_PERM_DESC' ,'Este parâmetro é apenas uma referência quando um item é criado. Altere as permissões diretamente no elFinder.<br>ex. 7cc: Owner 7 = -rwu, Group c = hr--, Guest c = hr--' );
    define( $constpref.'_USE_GUEST_DIR' ,           'Ativar pasta de conta para usuários anônimos' );
    define( $constpref.'_USE_GUEST_DIR_DESC' ,      '' );
    define( $constpref.'_GUEST_DIR_PERM' ,          'Definir permissão de "Ativar pasta de conta para usuários anônimos"' );
    define( $constpref.'_GUEST_DIR_PERM_DESC' ,     'Este parâmetro é apenas uma referência quando um item é criado. Altere as permissões diretamente no elFinder.<br>ex. 766: Owner 7 = -rwu, Group 6 = -rw-, Guest 6 = -rw-' );
    define( $constpref.'_GUEST_DIR_ITEM_PERM' ,     'Definir permissão de novos itens em "Ativar pasta de conta para usuários anônimos"' );
    define( $constpref.'_GUEST_DIR_ITEM_PERM_DESC' ,'Este parâmetro é apenas uma referência quando um item é criado. Altere as permissões diretamente no elFinder.<br>ex. 744: Owner 7 = -rwu, Group 4 = -r--, Guest 4 = -r--' );
    define( $constpref.'_USE_GROUP_DIR' ,           'Ativar pasta para cada grupo' );
    define( $constpref.'_USE_GROUP_DIR_DESC' ,      '' );
    define( $constpref.'_GROUP_DIR_PARENT' ,        'Set parent holder name for "Ativar pasta para cada grupo"' );
    define( $constpref.'_GROUP_DIR_PARENT_DESC' ,   '' );
    define( $constpref.'_GROUP_DIR_PARENT_NAME' ,   'Nome do diretório pai para grupos');
    define( $constpref.'_GROUP_DIR_PERM' ,          'Definir permissão de "Ativar pasta para cada grupo"' );
    define( $constpref.'_GROUP_DIR_PERM_DESC' ,     'Este parâmetro é apenas uma referência quando um item é criado. Altere as permissões diretamente no elFinder.<br>ex. 768: Owner 7 = -rwu, Group 6 = -rw-, Guest 8 = h---' );
    define( $constpref.'_GROUP_DIR_ITEM_PERM' ,     'Definir permissão de novos itens em "Ativar pasta para cada grupo"' );
    define( $constpref.'_GROUP_DIR_ITEM_PERM_DESC' ,'Este parâmetro é apenas uma referência quando um item é criado. Altere as permissões diretamente no elFinder.<br>ex. 748: Owner 7 = -rwu, Group 4 = -r--, Guest 8 = h---' );

    define( $constpref.'_UPLOAD_ALLOW_ADMIN' ,      'Tipos MIME permitidos para uploads d administrador' );
    define( $constpref.'_UPLOAD_ALLOW_ADMIN_DESC' , 'Especifique os tipos MIME, separados por um espaço.<br>Valor para permitir todos: all. Valor para desabilitar tudo : none<br>Exemplo: image text/plain' );
    define( $constpref.'_AUTO_RESIZE_ADMIN' ,       'Redimensionar automaticamente uploads do administrador' );
    define( $constpref.'_AUTO_RESIZE_ADMIN_DESC' ,  'Valor em pixels para redimensionar uma imagem automaticamente para que ela caiba no tamanho do retângulo especificado no momento do upload.<br>Deixe em branco para desativar o redimensionamento automático.' );
    define( $constpref.'_UPLOAD_MAX_ADMIN' ,        'Tamanho máximo de arquivo permitido para Admin' );
    define( $constpref.'_UPLOAD_MAX_ADMIN_DESC',    'Limite o upload com um tamanho máximo de arquivo. Deixe em branco ou defina "0" para ilimitado. Exemplo de valor máximo: 10M' );

    define( $constpref.'_SPECIAL_GROUPS' ,          'Grupos especiais' );
    define( $constpref.'_SPECIAL_GROUPS_DESC' ,     'Selecione os grupos para os quais deseja definir permissões específicas. Seleção múltipla' );
    define( $constpref.'_UPLOAD_ALLOW_SPGROUPS' ,   'Tipos MIME permitidos para grupos especiais' );
    define( $constpref.'_UPLOAD_ALLOW_SPGROUPS_DESC','' );
    define( $constpref.'_AUTO_RESIZE_SPGROUPS' ,    'Redimensionar automaticamente uploads de grupos especiais (valor em pixels)' );
    define( $constpref.'_AUTO_RESIZE_SPGROUPS_DESC','' );
    define( $constpref.'_UPLOAD_MAX_SPGROUPS' ,     'Tamanho máximo de arquivo permitido para grupos especiais' );
    define( $constpref.'_UPLOAD_MAX_SPGROUPS_DESC', '' );

    define( $constpref.'_UPLOAD_ALLOW_USER' ,       'Tipos MIME permitidos para usuários registrados' );
    define( $constpref.'_UPLOAD_ALLOW_USER_DESC' ,  '' );
    define( $constpref.'_AUTO_RESIZE_USER' ,        'Redimensionar automaticamente uploads de usuários registrados (valor em pixels)' );
    define( $constpref.'_AUTO_RESIZE_USER_DESC',    '' );
    define( $constpref.'_UPLOAD_MAX_USER' ,         'Tamanho máximo de arquivo permitido para usuários registrados' );
    define( $constpref.'_UPLOAD_MAX_USER_DESC',     '' );

    define( $constpref.'_UPLOAD_ALLOW_GUEST' ,      'Tipos MIME permitidos para usuários anônimos' );
    define( $constpref.'_UPLOAD_ALLOW_GUEST_DESC' , '' );
    define( $constpref.'_AUTO_RESIZE_GUEST' ,       'Redimensionar automaticamente uploads de usuários anônimos (valor em pixels)' );
    define( $constpref.'_AUTO_RESIZE_GUEST_DESC',   '' );
    define( $constpref.'_UPLOAD_MAX_GUEST' ,        'Tamanho máximo de arquivo permitido para usuários anônimos' );
    define( $constpref.'_UPLOAD_MAX_GUEST_DESC',    '' );

    define( $constpref.'_DISABLE_PATHINFO' ,        '🚩 Desative "PATH_INFO" no URL de referência do arquivo' );
    define( $constpref.'_DISABLE_PATHINFO_DESC' ,   'Selecione "Sim" para servidores onde a variável de ambiente "PATH_INFO" não está disponível, por exemplo Links de imagem quebrados do NGINX.' );

    define( $constpref.'_EDIT_DISABLE_LINKED' ,     'Arquivos vinculados protegidos contra gravação' );
    define( $constpref.'_EDIT_DISABLE_LINKED_DESC' ,'Ativa automaticamente a "proteção contra gravação" de arquivos para evitar links quebrados e substituição inadvertida.' );

    define( $constpref.'_CHECK_NAME_VIEW' ,         'Correspondência URLs e referências autovinculadas' );
    define( $constpref.'_CHECK_NAME_VIEW_DESC' ,    'Se o nome do arquivo no URL de referência do arquivo não corresponder ao nome do arquivo registrado, retorne o erro "404 Not Found".' );

    define( $constpref.'_CONNECTOR_URL' ,           'URL do conector para conexão externa ou segura (opcional)' );
    define( $constpref.'_CONNECTOR_URL_DESC' ,      'Especifique a URL de connector.php para conexão a um site externo ou usar um ambiente seguro apenas para comunicação com o back-end.' );

    define( $constpref.'_CONN_URL_IS_EXT',          'URL do conector externo' );
    define( $constpref.'_CONN_URL_IS_EXT_DESC',     'Selecione "Sim" se o URL do conector especificado for um site externo ou<br>selecione "Não" se a URL do conector for SSL apenas para comunicação de back-end.<br>O site externo deve autorizar conexão a partir deste site.' );

    define( $constpref.'_ALLOW_ORIGINS',            'Permitir origem do domínio' );
    define( $constpref.'_ALLOW_ORIGINS_DESC',       'Defina os sites externos autorizados a conectar a este site, nome de domínios web separados por novas linhas<br>Exemplo de URL do site sem a última barra: "https://example.com"<br>A conexão SSL para comunicação de back-end requer o protocolo https : <strong>'.preg_replace('#^(https?://[^/]+).*$#', '$1', XOOPS_URL).'</strong>' );

    define( $constpref.'_UNZIP_LANG_VALUE' ,        'Local (idioma) para descompactar exec' );
    define( $constpref.'_UNZIP_LANG_VALUE_DESC' ,   '' );

    define( $constpref.'_AUTOSYNC_SEC_ADMIN',       'Intervalo de sincronização automática (Admin)' );
    define( $constpref.'_AUTOSYNC_SEC_ADMIN_DESC',  'Especifique o intervalo de tempo entre os ciclos de sincronização em segundos.' );

    define( $constpref.'_AUTOSYNC_SEC_SPGROUPS',    'Intervalo de sincronização automática (grupos especiais)' );
    define( $constpref.'_AUTOSYNC_SEC_SPGROUPS_DESC', '' );

    define( $constpref.'_AUTOSYNC_SEC_USER',        'Intervalo de sincronização automática (usuário registrado)' );
    define( $constpref.'_AUTOSYNC_SEC_USER_DESC',   '' );

    define( $constpref.'_AUTOSYNC_SEC_GUEST',       'Intervalo de sincronização automática (usuários anônimos)' );
    define( $constpref.'_AUTOSYNC_SEC_GUEST_DESC',  '' );

    define( $constpref.'_AUTOSYNC_START',           'Sincronize automaticamente o mais rápido possível' );
    define( $constpref.'_AUTOSYNC_START_DESC',      'Inicie e pare a sincronização automática usando "recarregar" no menu de contexto.' );

    define( $constpref.'_FFMPEG_PATH',              'Caminho para o comando ffmpeg' );
    define( $constpref.'_FFMPEG_PATH_DESC',         'Especifique o caminho quando ffmpeg for necessário.' );

    define( $constpref.'_DEBUG' ,                   'Ativar modo de depuração' );
    define( $constpref.'_DEBUG_DESC' ,              'O X-elFinder lê um arquivo individual em vez de "elfinder.min.css" e "elfinder.min.js"<br>Além disso, as informações de depuração são incluídas na resposta do JavaScript.<br>A depuração não é recomendada no ambiente de produção.' );

// admin/dropbox.php
    define( $constpref.'_DROPBOX_STEP1' ,        'Etapa 1: criar aplicativo');
    define( $constpref.'_DROPBOX_GOTO_APP' ,     'Crie o aplicativo no seguinte link (Dropbox.com), adquira a chave do aplicativo e o segredo do aplicativo e defina como valores de "%s" e "%s" de Preferências.');
    define( $constpref.'_DROPBOX_GET_TOKEN' ,    'Obter "Token de aplicativo do Dropbox"');
    define( $constpref.'_DROPBOX_STEP2' ,        'Passo 2: Acesse o Dropbox e aprove');
    define( $constpref.'_DROPBOX_GOTO_CONFIRM' , 'Vá para o seguinte link (Dropbox.com) e aprove aplicativo.');
    define( $constpref.'_DROPBOX_CONFIRM_LINK' , 'Vá para Dropbox.com e aprove um aplicativo. ');
    define( $constpref.'_DROPBOX_STEP3' ,        'Passo 3: Concluído. Define as preferências.');
    define( $constpref.'_DROPBOX_SET_PREF' ,     'Defina o seguinte valor para cada item de Preferências.');

// admin/googledrive.php
    define( $constpref.'_GOOGLEDRIVE_GET_TOKEN', 'Google Drive API');

// admin/composer_update.php
    define( $constpref.'_COMPOSER_UPDATE' ,       'Atualizar Vendor - Composer');
    define( $constpref.'_COMPOSER_RUN_UPDATE' ,   'Executar atualização do Composer');
    define( $constpref.'_COMPOSER_UPDATE_STARTED','Atualização iniciada. Aguarde até que o sistema exiba a mensagem "Atualização concluída" ...');
    define( $constpref.'_COMPOSER_DONE_UPDATE' ,  'A atualização de "vendor"" foi concluída.');
    define( $constpref.'_COMPOSER_UPDATE_ERROR' , 'O driver pode não estar instalado ou pode não estar instalado corretamente!');
    define( $constpref.'_COMPOSER_UPDATE_FAIL',   'O arquivo "vendor" não existe : %s ');
    define( $constpref.'_COMPOSER_UPDATE_SUCCESS','O arquivo "vendor" existe  %s .');
    define( $constpref.'_COMPOSER_UPDATE_TIME' ,  'Isso pode levar algum tempo, dependendo da conexão Internet!');
    define( $constpref.'_COMPOSER_UPDATE_HELP' ,  'Execute Composer para atualizar os pacotes necessários e gere novamente um arquivo composer.block');
}
