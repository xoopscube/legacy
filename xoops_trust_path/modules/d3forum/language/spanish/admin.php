<?php
//Traducción al español para ImpressCMS por debianus

define('_MD_A_MYMENU_MYTPLSADMIN','Plantillas');
define('_MD_A_MYMENU_MYBLOCKSADMIN','Bloques/Permisos');
define('_MD_A_MYMENU_MYPREFERENCES','Preferencias');

// forum_access and category_access
define('_MD_A_D3FORUM_LABEL_SELECTFORUM','Seleccione un foro');
define('_MD_A_D3FORUM_LABEL_SELECTCATEGORY','Seleccione una categoría');
define('_MD_A_D3FORUM_H2_GROUPPERMS','Permisos relativos a los grupos');
define('_MD_A_D3FORUM_H2_USERPERMS','Permisos relativos a los usuarios');
define('_MD_A_D3FORUM_TH_CAN_READ','Leer');
define('_MD_A_D3FORUM_TH_CAN_POST','Enviar');
define('_MD_A_D3FORUM_TH_CAN_EDIT','Modificar');
define('_MD_A_D3FORUM_TH_CAN_DELETE','Eliminar');
define('_MD_A_D3FORUM_TH_POST_AUTO_APPROVED','Autoaprobar');
define('_MD_A_D3FORUM_TH_IS_MODERATOR','Moderador');
define('_MD_A_D3FORUM_TH_CAN_MAKEFORUM','Crear foros');
define('_MD_A_D3FORUM_TH_UID','Uid');
define('_MD_A_D3FORUM_TH_UNAME','Nombre');
define('_MD_A_D3FORUM_TH_GROUPNAME','Grupo');
define('_MD_A_D3FORUM_NOTICE_ADDUSERS','Añada cada "uid" o nombre.');
define('_MD_A_D3FORUM_ERR_CREATECATEGORYFIRST','Debe crear primero una categoría');
define('_MD_A_D3FORUM_ERR_CREATEFORUMFIRST','Debe crear un foro primero');

// advanced
define('_MD_A_D3FORUM_H2_SYNCALLTABLES','Sincronizar las informaciones redundantes');
define('_MD_A_D3FORUM_MAX_TOPIC_ID','Max. temas id');
define('_MD_A_D3FORUM_LABEL_SYNCTOPICS_START','sincronizar desde el tema');
define('_MD_A_D3FORUM_LABEL_SYNCTOPICS_NUM','hasta el tema');
define('_MD_A_D3FORUM_BTN_DOSYNCTABLES','Sincronizar');
define('_MD_A_D3FORUM_FMT_SYNCTOPICSDONE','%s temas han sido sincronizados.');
define('_MD_A_D3FORUM_MSG_SYNCTABLESDONE','Sincronización realizada con éxito.');
define('_MD_A_D3FORUM_HELP_SYNCALLTABLES','Ejecute esta acción si sus foros muestran datos contradictorios. Debe hacerlo después de haber llevado a cabo una importación de datos desde otros módulos.');
define('_MD_A_D3FORUM_H2_IMPORTFROM','Importar');
define('_MD_A_D3FORUM_H2_COMIMPORTFROM','Importar desde los comentarios');
define('_MD_A_D3FORUM_LABEL_SELECTMODULE','Seleccione un módulo');
define('_MD_A_D3FORUM_BTN_DOIMPORT','Hacer la importación');
define('_MD_A_D3FORUM_CONFIRM_DOIMPORT','¿Está de acuerdo?');
define('_MD_A_D3FORUM_MSG_IMPORTDONE','La importación se realizó con éxito.');
define('_MD_A_D3FORUM_MSG_COMIMPORTDONE','Los comentarios del módulo son importados para la integración de comentarios en este módulo.');
define('_MD_A_D3FORUM_ERR_INVALIDMID','Ha especificado un módulo equivocado para llevar a cabo la importación.');
define('_MD_A_D3FORUM_ERR_SQLONIMPORT','Hubo un fallo al realizar la importación. Debe comprobar las versiones de cada uno de los módulos.');
define('_MD_A_D3FORUM_HELP_IMPORTFROM','Puede importar desde newbb1, xhnewbb y desde otros foros de d3forum. Tenga en cuenta que la copia puede no ser perfecta y en particular debe comprobar los permisos. También debe saber que todos los datos en el módulo de origen se perderán cuando se ejecute la importación.');
define('_MD_A_D3FORUM_HELP_COMIMPORTFROM','Los comentarios de ImpressCMS serán importados como mensajes de los foros de d3forum. Tiene que activar la característica de integración de comentarios para usarlos, ya sea editando las plantillas, modificando las preferencias, etc.');

// post_histories
define('_MD_A_D3FORUM_H2_POSTHISTORIES','Historial de la modificación/eliminación de mensajes');
define('_MD_A_D3FORUM_LINK_REFERDELETED','Eliminados');

?>