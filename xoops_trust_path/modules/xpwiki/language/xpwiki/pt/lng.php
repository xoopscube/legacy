<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: lng.php,v 1.7 2011/12/08 07:01:00 nao-pon Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki message file (Portuguese)


// NOTE: Encoding of this file, must equal to encoding setting

// Q & A Verification
$root->riddles = array(
//	'Question' => 'Answer',
	'a, b, c e a próxima letra é?' => 'd',
	'1 + 1 = ?' => '2',
	'10 - 5 = ?' => '5',
	'a, *, c ... o que é o *?' => 'b',
	'Por favor, reescreva "ABC" em letras minusculas.' => 'abc',
);

///////////////////////////////////////
// Page titles
$root->_title_cannotedit = ' $1 não é editável';
$root->_title_edit       = 'Edição da $1';
$root->_title_preview    = 'Vizualização da $1';
$root->_title_collided   = 'Ocorreu um conflito na atualização da $1.';
$root->_title_updated    = ' $1 foi atualizada';
$root->_title_deleted    = ' $1 foi excluida';
$root->_title_help       = 'Ajuda';
$root->_title_invalidwn  = 'Este não é um Wikiname válido';
$root->_title_backuplist = 'Lista de Backup';
$root->_title_ng_riddle  = 'Falha na verificação da pegunta e resposta de verificação.<br />Vizualização da  $1';
$root->_title_backlink   = 'Links de retorno de: %s';

///////////////////////////////////////
// Messages
$root->_msg_unfreeze = 'Descongelar';
$root->_msg_preview  = 'Clique no botão em baixo da pÁgina para confirmar as mudanças, ';
$root->_msg_preview_delete = '(O conteúdo da página está vazio. Atualize a exclusão desta página.)';
$root->_msg_collided = 'Isto indica que alguém atualizou esta página enquanto você estava editando-a.<br />
 + é colocado no começo da linha que foi mais recentemente edicionada.<br />
 ! é colocado no começo da linha que foi possivelmente atualizada.<br />
 Edite essas linha e envie novamente.';

$root->_msg_collided_auto = 'Isto indica que alguém atualizou esta página enquanto você estava editando-a.<br /> O conflito foi corrigido automaticamente, mas podem existir alguns problemas com esta página.<br />
 Pressione em [Atualização]para confirmar as mudanças na página.<br />';


$root->_msg_invalidiwn  = ' $1 não é válida $2.';
$root->_msg_invalidpass = 'Senha inválida.';
$root->_msg_notfound    = 'A página não foi encontrada.';
$root->_msg_addline     = 'A linha adicionada é <span class="diff_added">Nesta cor</span>.';
$root->_msg_delline     = 'A linha excluida é <span class="diff_removed">Nesta cor</span>.';
$root->_msg_goto        = 'Ir para $1.';
$root->_msg_andresult   = 'Na página <strong> $2</strong>, <strong> $3</strong> páginas que contém todos os termos $1 foram encontrados.';
$root->_msg_orresult    = 'Na página <strong> $2</strong>, <strong> $3</strong> páginas que contém pelo menos um dos termos $1 foram encontrados.';
$root->_msg_notfoundresult = 'Nenhuma página com o conteúdo $1 foi encontrada.';
$root->_msg_symbol      = 'Simbolos';
$root->_msg_other       = 'Outros';
$root->_msg_help        = 'Vizualizar texto com as regras de formatação';
$root->_msg_week        = array('Dom','Seg','Ter','Qua','Qui','Sex','Sab');
$root->_msg_content_back_to_top = '<div class="jumpmenu"><a href="#'.$root->mydirname.'_navigator" title="Página Top"><img src="'.$const['LOADER_URL'].'?src=arrow_up.png" alt="Página Inicial" width="16" height="16" /></a></div>';
$root->_msg_word        = 'Os termos desta busca foram destacados:';
$root->_msg_not_readable   = 'Você não permissão de leitura.';
$root->_msg_not_editable   = 'Você não tem permissão para editar.';

///////////////////////////////////////
// Symbols
$root->_symbol_anchor   = 'src:anchor.png,width:12,height:12';
$root->_symbol_noexists = '<img src="'.$const['IMAGE_DIR'].'paraedit.png" alt="Editar" height="9" width="9" />';

///////////////////////////////////////
// Form buttons
$root->_btn_preview   = 'Vizualização';
$root->_btn_repreview = 'Vizualizar novamente';
$root->_btn_update    = 'Atualização';
$root->_btn_cancel    = 'Cancelar';
$root->_btn_notchangetimestamp = 'Não mudar dia e hora da gravação';
$root->_btn_addtop    = 'Adicionar ao topo da página';
$root->_btn_template  = 'Usar página como modelo';
$root->_btn_load      = 'Carregar';
$root->_btn_edit      = 'Editar';
$root->_btn_delete    = 'Excluir';
$root->_btn_reading   = 'Leitura da página inicial';
$root->_btn_alias     = 'Página Alias <span class="edit_form_note">(Dividida com "<span style="color:red;font-weight:bold;font-size:120%;">:</span>"[Colon])</span>';
$root->_btn_alias_lf  = 'Página Alias <span class="edit_form_note">(Dividida com "<span style="color:red;font-weight:bold;font-size:120%;">Each line</span>")</span>';
$root->_btn_riddle    = 'Verificação da Pergunta e Resposta: <span class="edit_form_note">Por favor, responta a pergunta antes de atualizar a página (desnecessária a vizualização).</span>';
$root->_btn_pgtitle   = 'Título da página<span class="edit_form_note">(Automático em branco)</span>';
$root->_btn_pgorder   = 'Ordenação da página<span class="edit_form_note">(0-9 Decimal Padrão:1 )</span>';
$root->_btn_other_op  = 'Mostrar detalhamento dos itens informados.';
$root->_btn_emojipad  = 'Pictogram pad';
$root->_btn_source    = 'Details';

///////////////////////////////////////
// Authentication
$root->_title_cannotread = ' $1 leitura não permitida';
$root->_msg_auth         = 'PukiWikiAuth';

///////////////////////////////////////
// Page name
$root->rule_page = 'Regras de formatação';	// Formatting rules
$root->help_page = 'Ajuda';		// Help

///////////////////////////////////////
// TrackBack (REMOVED)
$root->_tb_date   = 'F j, Y, g:i A';

/////////////////////////////////////////////////
// No subject (article)
$root->_no_subject = 'Sem assunto';

/////////////////////////////////////////////////
// No name (article,comment,pcomment)
$root->_no_name = '';

/////////////////////////////////////////////////
// Title of the page contents list
$root->contents_title = 'Tabela de conteúdos';

/////////////////////////////////////////////////
// Skin
/////////////////////////////////////////////////

$root->_LANG['skin']['topage']    = 'Retornar a página';
$root->_LANG['skin']['add']       = 'Adicionar';
$root->_LANG['skin']['backup']    = 'Cópia de Segurança';
$root->_LANG['skin']['copy']      = 'Copiar';
$root->_LANG['skin']['diff']      = 'Diferença';
$root->_LANG['skin']['back']      = 'Histórico';
$root->_LANG['skin']['edit']      = 'Editar';
$root->_LANG['skin']['filelist']  = 'Nome dos arquivos das páginas';	// List of filenames
$root->_LANG['skin']['attaches']  = 'Anexos';
$root->_LANG['skin']['freeze']    = 'Congelar';
$root->_LANG['skin']['help']      = 'Ajuda';
$root->_LANG['skin']['list']      = 'Relação das Páginas';
$root->_LANG['skin']['list_s']    = 'Lista';
$root->_LANG['skin']['new']       = 'Nova Página';
$root->_LANG['skin']['new_s']     = 'Nova';
$root->_LANG['skin']['newsub']    = 'Nova Sub Página';
$root->_LANG['skin']['newsub_s']  = 'Sub';
$root->_LANG['skin']['menu']      = 'Menu';
$root->_LANG['skin']['header']    = 'Cabeçalho';
$root->_LANG['skin']['footer']    = 'Rodapé';
$root->_LANG['skin']['rdf']       = 'RDF das Últimas Alterações';
$root->_LANG['skin']['recent']    = 'Últimas  Alterações';	// RecentChanges
$root->_LANG['skin']['recent_s']  = 'Última';
$root->_LANG['skin']['refer']     = 'Referir';	// Show list of referer
$root->_LANG['skin']['reload']    = 'Baxar novamente';
$root->_LANG['skin']['rename']    = 'Renomear';	// Rename a page (and related)
$root->_LANG['skin']['rss']       = 'RSS das Últimas Alterações';
$root->_LANG['skin']['rss10']     = $root->_LANG['skin']['rss'] . ' (RSS 1.0)';
$root->_LANG['skin']['rss20']     = $root->_LANG['skin']['rss'] . ' (RSS 2.0)';
$root->_LANG['skin']['atom']      = $root->_LANG['skin']['rss'] . ' (RSS Atom)';
$root->_LANG['skin']['search']    = 'Busca';
$root->_LANG['skin']['search_s']  = 'Busca';
$root->_LANG['skin']['top']       = 'Página Inicial';	// Top page
$root->_LANG['skin']['trackback'] = 'Links de Retorno';	// Show list of trackback
$root->_LANG['skin']['unfreeze']  = 'Descongelar';
$root->_LANG['skin']['upload']    = 'Enviar';	// Attach a file
$root->_LANG['skin']['pginfo']    = 'Permissão';
$root->_LANG['skin']['comments']  = 'Comentários';
$root->_LANG['skin']['lastmodify']= 'Última Alteração';
$root->_LANG['skin']['linkpage']  = 'Links';
$root->_LANG['skin']['pagealias'] = 'Página Alias';
$root->_LANG['skin']['pageowner'] = 'Proprietário da Página';
$root->_LANG['skin']['siteadmin'] = 'Administrador do Site';
$root->_LANG['skin']['none']      = 'Nenhum';
$root->_LANG['skin']['pageinfo']  = 'Página de Informações';
$root->_LANG['skin']['pagename']  = 'Nome da Página';
$root->_LANG['skin']['readable']  = 'Pode Ler';
$root->_LANG['skin']['editable']  = 'Pode Editar';
$root->_LANG['skin']['groups']    = 'Grupos';
$root->_LANG['skin']['users']     = 'Usuários';
$root->_LANG['skin']['perm']['all']  = 'Todos os Visitantes';
$root->_LANG['skin']['perm']['none'] = 'Ninguém';
$root->_LANG['skin']['print']     = 'Vizualizar Impressão';
$root->_LANG['skin']['print_s']   = 'Imprimir';

///////////////////////////////////////
// Plug-in message
///////////////////////////////////////
// add.inc.php
$root->_title_add = 'Adicionar para $1';
$root->_msg_add   = 'Dois ou mais conteúdos de uma informação são adicionadas em uma nova linha nos conteúdos da página da atual edição.';
	// This message is such bad english that I don't understand it, sorry. --Bjorn De Meyer
    // I think i could translate this message into portuguese, but in english it is bad. --Leco(1)(Miraldo Ohse)

///////////////////////////////////////
// article.inc.php
$root->_btn_name    = 'Nome: ';
$root->_btn_article = 'Enviar';
$root->_btn_subject = 'Assunto: ';
$root->_msg_article_mail_sender = 'Autor: ';
$root->_msg_article_mail_page   = 'Página: ';

///////////////////////////////////////
// attach.inc.php
$root->_attach_messages = array(
	'msg_uploaded' => 'Enviado arquivo para  $1',
	'msg_deleted'  => 'Exluído o arquivo em  $1',
	'msg_freezed'  => 'O arquivo foi congelado.',
	'msg_unfreezed'=> 'O arquivo foi descongelado',
	'msg_renamed'  => 'O arquivo foi renomeado',
	'msg_upload'   => 'Enviar para $1',
	'msg_info'     => 'Informação do Arquivo',
	'msg_confirm'  => '<p>Excluir %s.</p>',
	'msg_list'     => 'Lista dos arquivos anexos',
	'msg_listpage' => 'O arquivo já esiste no $1',
	'msg_listall'  => 'Lista dos arquivos anexos de todoas as páginas',
	'msg_file'     => 'Arquivo anexo',
	'msg_maxsize'  => 'O tamanho máximo do arquivo é %s.',
	'msg_count'    => ' <span class="small">%sDL</span>',
	'msg_password' => 'Senha para este arquivo (requerido)',
	'msg_password2'=> 'Senha deste arquivo',
	'msg_adminpass'=> 'Senha do administrador',
	'msg_delete'   => 'Excluir arquivo.',
	'msg_backup'   => 'Fazer cópia de segurança',
	'msg_freeze'   => 'Congelar arquivo.',
	'msg_unfreeze' => 'Descongelar arquivo.',
	'msg_isfreeze' => 'O arquivo está congelado.',
	'msg_rename'   => 'Renomear',
	'msg_newname'  => 'Nome do novo arquivo',
	'msg_require'  => '(É necessário a senha especificada quando do carregamento.)',
	'msg_filesize' => 'Tamanho',
	'msg_date'     => 'Data',
	'msg_dlcount'  => 'Contador de acessos',
	'msg_md5hash'  => 'MD5 hash',
	'msg_page'     => 'Página',
	'msg_filename' => 'Nome do arquivo armazenado',
	'msg_owner'    => 'Proprietário',
	'err_noparm'   => 'Não foi possível enviar ou excluir o arquivo no $1',
	'err_exceed'   => 'O tamanho do arquivo é muito grande para $1',
	'err_exists'   => 'O arquivo já existe em $1',
	'err_notfound' => 'O arquivo não pode ser encontrado no $1',
	'err_noexist'  => 'O arquivo não existe.',
	'err_delete'   => 'O arquivo não pode ser excluido em $1',
	'err_rename'   => 'Este arquivo não pode ser renomeado',
	'err_password' => 'Senha errada.',
	'err_adminpass'=> 'A senha do administrador está errada',
	'err_nopage'   => 'Uma página "$1" não foi encontrada. Por favor, faça uma página antes.',
	'btn_upload'   => 'Enviar',
	'btn_upload_fm'=> 'Enviar Form',
	'btn_info'     => 'Informações',
	'btn_submit'   => 'Enviar',
	'msg_copyrighted'  => 'O arquivo anexo está protegido por copyrighting.',
	'msg_uncopyrighted'=> 'A proteção de copyright do arquivo anexado foi liberada.',
	'msg_copyright'  => 'O arquivo anexado foi protegido por copyrighting.',
	'msg_copyright0' => 'Este aquivo é meu ou está livre de copyright.',
	'err_copyright'  => 'Este arquivo não pode ser mostrado e baixado porque ele está protegido copyright.',
	'msg_noinline1'  => 'Proibida a exibição inline.',
	'msg_noinline0-1'=> 'Liberar a proibição de exibição inline.',
	'msg_noinline-1' => 'Permitida a exibição inline.',
	'msg_noinline01' => 'Liberar a exibição das permissões inline.',
	'msg_noinlined'  => 'As configurações da exibição inline dos arquivos anexos foi registrada.',
	'msg_unnoinlined'=> 'As configurações da exibição inline dos arquivos anexos foi liberada.',
	'msg_nopcmd'     => 'A operação não foi especificada.',
	'err_extension'=> 'A extensão do arquivo não pode ser anexada ao $1 porque não existe autorização do proprietário nesta página.',
	'msg_set_css'  => '$1 folha de estilo foi configurada.',
	'msg_unset_css'=> '$1 folha de estilo foi cancelada.',
	'msg_untar'    => 'UNTAR',
	'msg_search_updata'=> 'O dado enviado para esta página está procurando por.',
	'msg_paint_tool'=> 'Ferramenta de pintura',
	'msg_shi'      => 'SHI PAINTER',
	'msg_shipro'   => 'SHI PAINTER Pro',
	'msg_width'    => 'Largura',
	'msg_height'   => 'Altura',
	'msg_max'      => 'Tamanho máximo',
	'msg_do_paint' => 'Pintar',
	'msg_save_movie'=> 'Gravar animação',
	'msg_adv_setting'=> '--- Especificação estendida ---',
	'msg_init_image'=> 'O arquivo da imagem lido na tela (JPEG ou GIF)',
	'msg_fit_size' => 'Tamanho da tela combina com esta imagem.',
	'msg_extensions' => 'Extensão de arquivos que podem ser anexados ( $1 )',
	'msg_rotated_ok' => 'Imagem foi rodada.<br />Ela pode não ser mostrada corretamente por um navegador, caso não for baixada novamente.',
	'msg_rotated_ng' => 'Não foi possivel a imagem ser rodada.',
	'err_isflash' => 'Um arquivo Flash não pode ser enviado.',
	'msg_make_thumb' => 'Fazer uma miniatura.(Somente arquivo de imagem): ',
	'msg_sort_time' => 'Classificar pelo tempo',
	'msg_sort_name' => 'Classificar pelo nome',
	'msg_list_view' => 'Vizualizar lista',
	'msg_image_view' => 'Vizualizar imagem',
	'msg_insert' => 'Inserir',
	'msg_select_current' => ' (Atual)',
	'msg_select_useful' => 'Páginas para remessa',
	'msg_select_manyitems' => 'Páginas com muitos arquivos',
	'msg_noupload' => 'Não foi posspivel enviar alguns arquivos para $1.',
	'msg_send_mms' => 'Send by MMS Mail',
	'msg_drop_files_here' => 'Drop files here to upload',
	'msg_for_upload' => 'There is no authority uploaded to this page.<br />In order to upload, please choose a page like "<span class="attachable">This Style</span>" at the <img src="'.$const['LOADER_URL'].'?src=page_attach.png" alt="Page" /> page selection.',
);

///////////////////////////////////////
// back.inc.php
$root->_msg_back_word = 'Voltar';

///////////////////////////////////////
// backup.inc.php
$root->_title_backup_delete  = 'Deletar backup do $1';
$root->_title_backupdiff     = 'Backup diferença de $1(No. $2)';
$root->_title_backupnowdiff  = 'Backup diferença de $1 vs atual(No. $2)';
$root->_title_backupsource   = 'Backup fonte de $1(No. $2)';
$root->_title_backup         = 'Backup de $1(No. $2)';
$root->_title_pagebackuplist = 'Backup lista de of $1';
$root->_title_backuplist     = 'Backup lista';
$root->_msg_backup_deleted   = 'Backup de $1 foi excluido.';
$root->_msg_backup_adminpass = 'Por favor, informe a senha para excluir.';
$root->_msg_backuplist       = 'Lista de Backups';
$root->_msg_nobackup         = 'Não existem backup(s) de $1.';
$root->_msg_diff             = 'diferença';
$root->_msg_nowdiff          = 'diferença atual';
$root->_msg_source           = 'fonte';
$root->_msg_backup           = 'backup';
$root->_msg_view             = 'Vizualizar o $1.';
$root->_msg_deleted          = ' $1 foi exluida.';
$root->_msg_backupedit       = 'Editar Backup No.$1 como atual.';
$root->_msg_current          = 'Atual';
$root->_title_backuprewind   = 'Preparar para backup No.$2 of $1.';
$root->_title_dorewind       = 'Preparar conteúdo e gravação da data e hora com o tempo "$1"';
$root->_msg_rewind           = 'Preparar';
$root->_msg_dorewind         = 'Preparar para backup No.$1';
$root->_msg_rewinded         = 'Preparar no backup No.$1.';
$root->_msg_nobackupnum      = 'Perdido backup No.$1.';

///////////////////////////////////////
// calendar_viewer.inc.php
$root->_err_calendar_viewer_param2   = 'Segundo parâmetro errado.';
$root->_msg_calendar_viewer_right    = 'Próximo %d&gt;&gt;';
$root->_msg_calendar_viewer_left     = '&lt;&lt; Anterior %d';
$root->_msg_calendar_viewer_restrict = 'Devido ao bloqueio, a vizualização do calendário não referir para $1.';

///////////////////////////////////////
// calendar2.inc.php
$root->_calendar2_plugin_edit  = '[editar]';
$root->_calendar2_plugin_empty = '%s está vazio.';

///////////////////////////////////////
// comment.inc.php
$root->_btn_name    = 'Nome: ';
$root->_btn_comment = 'Postar Comentário';
$root->_msg_comment = 'Comentário: ';
$root->_title_comment_collided = 'Na atualização $1, ocorreu um conflito.';
$root->_msg_comment_collided   = 'Parece que alguém atualizou a página que você estava editando.<br />
 O comentário foi adicionado, embora possa ter sido inserido em uma posição errada.<br />';

///////////////////////////////////////
// deleted.inc.php
$root->_deleted_plugin_title = 'Lista das páginas excluidas';
$root->_deleted_plugin_title_withfilename = 'Lista das páginas excluidas (com o nome do arquivo)';

///////////////////////////////////////
// diff.inc.php
$root->_title_diff         = 'Diferença do $1';
$root->_title_diff_delete  = 'Exclusão diferença de $1';
$root->_msg_diff_deleted   = 'Diferença de $1 foi excluida.';
$root->_msg_diff_adminpass = 'Por favor, insira a senha para excluir.';

///////////////////////////////////////
// filelist.inc.php (list.inc.php)
$root->_title_filelist = 'Lista de pagina de arquivos';

///////////////////////////////////////
// freeze.inc.php
$root->_title_isfreezed = ' $1 já está congelado';
$root->_title_freezed   = ' $1 está congelado.';
$root->_title_freeze    = 'Congelado  $1';
$root->_msg_freezing    = 'Por favor, informe a página para congelar.';
$root->_btn_freeze      = 'Congelar';

///////////////////////////////////////
// include.inc.php
$root->_msg_include_restrict = 'Devido ao bloqueio, $1 não pode ser incluido.';

///////////////////////////////////////
// insert.inc.php
$root->_btn_insert = 'adicionar';

///////////////////////////////////////
// interwiki.inc.php
$root->_title_invalidiwn = 'Este não é um InterWikiName válido';

///////////////////////////////////////
// list.inc.php
$root->_title_list = 'Lista de páginas';

///////////////////////////////////////
// ls2.inc.php
$root->_ls2_err_nopages = '<p>Não existe página criança em \' $1\'</p>';
$root->_ls2_msg_title   = 'Lista de páginas que começam com \' $1\'';

///////////////////////////////////////
// memo.inc.php
$root->_btn_memo_update = 'Atualização';

///////////////////////////////////////
// navi.inc.php
$root->_navi_prev = 'Anterior';
$root->_navi_next = 'Próxima';
$root->_navi_up   = 'Em cima';
$root->_navi_home = 'Página Inicial';

///////////////////////////////////////
// newpage.inc.php
$root->_msg_newpage = 'Nova página';

///////////////////////////////////////
// paint.inc.php
$root->_paint_messages = array(
	'field_name'    => 'Nome',
	'field_filename'=> 'Nome do Arquivo',
	'field_comment' => 'Comentário',
	'btn_submit'    => 'Pintar',
	'msg_max'       => '(Max %d x %d)',
	'msg_title'     => 'Pintar e anexar para  $1',
	'msg_title_collided' => 'Durante a atualização $1, houve um conflito.',
	'msg_collided'  => 'Parece que alguém atualizou está página enquanto você estava editando-a.<br />
 A imagem e o comentário foram atualizados nesta página, mas pode haver um problema.<br />'
);

///////////////////////////////////////
// pcomment.inc.php
$root->_pcmt_messages = array(
	'btn_name'       => 'Nome: ',
	'btn_comment'    => 'Postar Comentário',
	'msg_comment'    => 'Comentário: ',
	'msg_recent'     => 'Mostrar últimos %d comentários.',
	'msg_all'        => 'Ir para a página de comentários.',
	'msg_none'       => 'Não existe comentário.',
	'title_collided' => 'Na atualização $1, houve um conflito.',
	'msg_collided'   => 'Parece que alguém atualizou está página enquanto você estava editando-a.<br />
	O comentário foi adicionado na página, mas pode haver um problema.<br />',
	'err_pagename'   => '[[%s]] : não é um nome de página válido.',
);
$root->_msg_pcomment_restrict = 'Devido ao bloqueio, nenhum comentário pode ser lido de $1 em todos.';

///////////////////////////////////////
// popular.inc.php
$root->_popular_plugin_frame       = '<h5>Popular(%1$d)%3$s</h5><div>%2$s</div>';
$root->_popular_plugin_today_frame = '<h5>Hoje\'s(%1$d)%3$s</h5><div>%2$s</div>';
$root->_popular_plugin_yesterday_frame = '<h5>Ontem\'s(%1$d)%3$s</h5><div>%2$s</div>';

///////////////////////////////////////
// recent.inc.php
$root->_recent_plugin_frame = '<h5>%s Último(%d)</h5>
 <div>%s</div>';

///////////////////////////////////////
// referer.inc.php
$root->_referer_msg = array(
	'msg_H0_Refer'       => 'Referer',
	'msg_Hed_LastUpdate' => 'Última atualização',
	'msg_Hed_1stDate'    => 'Primeiro registro',
	'msg_Hed_RefCounter' => 'Ref Contagem',
	'msg_Hed_Referer'    => 'Referer',
	'msg_Fmt_Date'       => 'F j, Y, g:i A',
	'msg_Chr_uarr'       => '&uArr;',
	'msg_Chr_darr'       => '&dArr;',
);

///////////////////////////////////////
// rename.inc.php
$root->_rename_messages  = array(
	'err'            => '<p>Erro:%s</p>',
	'err_nomatch'    => 'Nenhuma página correspondente',
	'err_notvalid'   => 'O novo nome é inválido.',
	'err_adminpass'  => 'Senha do administrador incorreta.',
	'err_notpage'    => '%s não é um nome de página válido.',
	'err_norename'   => 'Não foi possível renomear %s.',
	'err_already'    => 'As seguintes páginas já existem.%s',
	'err_already_below' => 'Os seguintes arquivos já existem.',
	'msg_title'      => 'Renomear página',
	'msg_page'       => 'Especificar o nome da fonte da página',
	'msg_regex'      => 'Renomear com expressões regular.',
	'msg_regex'      => 'Expressões Regular',
	'msg_part_rep'   => 'Recolocar partial matches',
	'msg_related'    => 'Páginas relacionadas',
	'msg_do_related' => 'Uma página relacionada é também renomeada.',
	'msg_rename'     => 'Renomear %s',
	'msg_oldname'    => 'Nome da página atual',
	'msg_newname'    => 'Nome da nova página',
	'msg_adminpass'  => 'Senha do administrador',
	'msg_arrow'      => '->',
	'msg_exist_none' => 'A página não é processada quando ela já existe.',
	'msg_exist_overwrite' => 'A página é subscrita quando ela já existe.',
	'msg_confirm'    => 'Os seguintes arquivos serão renomeados.',
	'msg_result'     => 'Os seguintes arquivos foram subscritos.',
	'btn_submit'     => 'Enviar',
	'btn_next'       => 'Próximo'
);

///////////////////////////////////////
// search.inc.php
$root->_title_search  = 'Busca';
$root->_title_result  = 'Buscar resultado de  $1';
$root->_msg_searching = 'As palavras-chave são case-insenstive, e são pesquisadas em todas as páginas.';
$root->_btn_search    = 'Busca';
$root->_btn_and       = 'E';
$root->_btn_or        = 'OU';
$root->_search_pages  = 'Buscar em páginas começadas por $1';
$root->_search_all    = 'Buscar em todas as páginas';

///////////////////////////////////////
// source.inc.php
$root->_source_messages = array(
	'msg_title'    => 'Fonte de $1',
	'msg_notfound' => ' $1 não foi encontrado.',
	'err_notfound' => 'Não foi possivel mostrar a fonte da página.'
);

///////////////////////////////////////
// template.inc.php
$root->_msg_template_start   = 'Iniciar:<br />';
$root->_msg_template_end     = 'Final:<br />';
$root->_msg_template_page    = '$1/cópia';
$root->_msg_template_refer   = 'Página:';
$root->_msg_template_force   = 'Editar com um nome de página que já existe';
$root->_err_template_already = ' $1 já existe.';
$root->_err_template_invalid = ' $1 não é um nome de página válido.';
$root->_btn_template_create  = 'Criar';
$root->_title_templatei      = 'Criar uma nova página usando $1 como um modelo.';

///////////////////////////////////////
// tracker.inc.php
$root->_tracker_messages = array(
	'msg_list'   => 'Lista de itens do $1',
	'msg_back'   => '<p> $1</p>',
	'msg_limit'  => 'Top  $2 resultados fora de $1.',
	'btn_page'   => 'Página',
	'btn_name'   => 'Nome',
	'btn_real'   => 'Nome real',
	'btn_submit' => 'Adicionar',
	'btn_date'   => 'Data',
	'btn_refer'  => 'Página Refer',
	'btn_base'   => 'Página Base',
	'btn_update' => 'Atualização',
	'btn_past'   => 'Passado',
);

///////////////////////////////////////
// unfreeze.inc.php
$root->_title_isunfreezed = ' $1 não está congelado';
$root->_title_unfreezed   = ' $1 foi descongelado.';
$root->_title_unfreeze    = 'Descongelar  $1';
$root->_msg_unfreezing    = 'Por favor, informe a senha para o descongelamento.';
$root->_btn_unfreeze      = 'Descongelar';

///////////////////////////////////////
// versionlist.inc.php
$root->_title_versionlist = 'Lista de versão';

///////////////////////////////////////
// vote.inc.php
$root->_vote_plugin_choice = 'Seleção';
$root->_vote_plugin_votes  = 'Votar';

///////////////////////////////////////
// yetlist.inc.php
$root->_title_yetlist = 'Lista de páginas que ainda não foram criadas.';
$root->_err_notexist  = 'Todas as páginas foram criadas.';
