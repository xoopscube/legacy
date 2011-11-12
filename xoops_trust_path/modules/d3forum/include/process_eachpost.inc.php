<?php

	// can_vote
	$can_vote = ( ($uid || $xoopsModuleConfig['guest_vote_interval']) && $xoopsModuleConfig['use_vote'] == 1 ) ? true : false ;

	// invisible
	if( $post_row['invisible'] || ! $post_row['approval'] ) {
		if( $isadminormod ) {
			// $post_row['icon'] = $post_row['approval'] ? 8 : 9 ;
		} else {
			$post_row['subject'] = $post_row['post_text'] = $post_row['guest_name'] = $post_row['approval'] ? _MD_D3FORUM_ALT_INVISIBLE : _MD_D3FORUM_ALT_UNAPPROVAL ;
			$post_row['uid'] = $post_row['icon'] = $post_row['attachsig'] = 0 ;
			$post_row['guest_email'] = $post_row['guest_url'] = $post_row['guest_trip'] = $post_row['poster_ip'] = $post_row['modifier_ip'] = '' ;
			$can_vote = false ;
		}
	}

	// get this poster's object
	$user_handler =& xoops_gethandler( 'user' ) ;
	$poster_obj =& $user_handler->get( intval( $post_row['uid'] ) ) ;
	if( is_object( $poster_obj ) ) {
		// active user's post
		// naao from
		if ($xoopsModuleConfig['use_name'] == 1 && $poster_obj->getVar( 'name' ) ) {
			$poster_uname4disp = $poster_obj->getVar( 'name' ) ;
		} else {
			$poster_uname4disp = $poster_obj->getVar( 'uname' ) ;
		}
		// naao to
		$poster_regdate = $poster_obj->getVar( 'user_regdate' ) ;
		$poster_from4disp = $myts->makeTboxData4Show( $poster_obj->getVar( 'user_from' ) , 1 ) ;
		$poster_rank = $poster_obj->rank() ;
		$poster_rank_title4disp = htmlspecialchars( @$poster_rank['title'] , ENT_QUOTES ) ;
		$poster_rank_image4disp = htmlspecialchars( @$poster_rank['image'] , ENT_QUOTES ) ;
		$poster_is_online = $poster_obj->isOnline() ;
		$poster_posts_count = intval( $poster_obj->getVar( 'posts' ) ) ;

		// avatar
		if( is_file( XOOPS_UPLOAD_PATH.'/'.$poster_obj->getVar( 'user_avatar' ) ) ) {
			list( $avatar_width , $avatar_height , $avatar_type , $avatar_attr ) = getimagesize( XOOPS_UPLOAD_PATH.'/'.$poster_obj->getVar( 'user_avatar' ) ) ;
			$poster_avatar = array(
				'path' => htmlspecialchars( $poster_obj->getVar( 'user_avatar' ) , ENT_QUOTES ) ,
				'width' => $avatar_width ,
				'height' => $avatar_height ,
				'type' => $avatar_type ,
				'attr' => $avatar_attr ,
			) ;
		} else {
			$poster_avatar = array() ;
		}

		// signature
		if( $xoopsModuleConfig['allow_sig'] && $post_row['attachsig'] ) {
			$signature4disp = $myts->displayTarea( $poster_obj->getVar('user_sig', 'N'), 0, 1, 1, $xoopsModuleConfig['allow_sigimg'] ) ;
		} else {
			$signature4disp = '' ;
		}

		// permissions
		$can_reply = ( $topic_row['topic_locked'] || $post_row['invisible'] || ! $post_row['approval'] ) ? false : $can_post ;
		if( $isadminormod ) {
			$can_edit = true ;
			$can_delete = true ;
		} else if( $post_row['uid'] == $uid ) {
			$can_edit = $forum_permissions[ $forum_id ]['can_edit'] && time() < $post_row['post_time'] + $xoopsModuleConfig['selfeditlimit'] ? true : false ;
			$can_delete = $forum_permissions[ $forum_id ]['can_delete'] && time() < $post_row['post_time'] + $xoopsModuleConfig['selfdellimit'] ? true : false ;
		} else {
			$can_edit = false ;
			$can_delete = false ;
		}
	} else {
		// guest or quitted or hidden user's post
		$poster_uname4disp = empty( $post_row['guest_name'] ) ? $xoopsModuleConfig['anonymous_name'] : htmlspecialchars( $post_row['guest_name'] , ENT_QUOTES ) ;
		$poster_regdate = $post_row['post_time'] ;
		$poster_from4disp = '' ;
		$poster_rank_title4disp = '' ;
		$poster_rank_image4disp = '' ;
		$poster_is_online = false ;
		$poster_avatar = array() ;
		$poster_posts_count = 0 ;

		// signature
		$signature4disp = '' ;

		// permissions
		$can_reply = ( $topic_row['topic_locked'] || $post_row['invisible'] || ! $post_row['approval'] ) ? false : $can_post ;
		if( $isadminormod ) {
			$can_edit = true ;
			$can_delete = true ;
		} else if( $post_row['uid_hidden'] && $post_row['uid_hidden'] == $uid  ) {
			$can_edit = $forum_permissions[ $forum_id ]['can_edit'] && time() < $post_row['post_time'] + $xoopsModuleConfig['selfeditlimit'] ? true : false ;
			$can_delete = $forum_permissions[ $forum_id ]['can_delete'] && time() < $post_row['post_time'] + $xoopsModuleConfig['selfdellimit'] ? true : false ;
		} else if( $uid > 0 ) {
			// normal user cannot touch guest's post
			$can_edit = false ;
			$can_delete = false ;
		} else {
			// guest can delete posts by password
			$can_edit = false ;
			$can_delete = $post_row['guest_pass_md5'] && $forum_permissions[ $forum_id ]['can_delete'] && time() < $post_row['post_time'] + $xoopsModuleConfig['selfdellimit'] ? true : false ;
		}
	}

	// d3comment object
	if( ! empty( $forum_row['forum_external_link_format'] ) ) $d3com =& d3forum_main_get_comment_object( $mydirname , $forum_row['forum_external_link_format'] ) ;
	else $d3com = false ;

	// d3comment overridings
	if( is_object( $d3com ) ) {
		$can_vote = $d3com->canVote( $topic_row['topic_external_link_id'] , $can_vote , $post_row['post_id'] ) ;
		$can_post = $d3com->canPost( $topic_row['topic_external_link_id'] , $can_post ) ;
		$can_reply = $d3com->canReply( $topic_row['topic_external_link_id'] , $can_reply , $post_row['post_id'] ) ;
		$can_edit = $d3com->canEdit( $topic_row['topic_external_link_id'] , $can_edit , $post_row['post_id'] ) ;
		$can_delete = $d3com->canDelete( $topic_row['topic_external_link_id'] , $can_delete , $post_row['post_id'] ) ;
		$need_approve = $d3com->needApprove( $topic_row['topic_external_link_id'] , $need_approve ) ;
	}

?>