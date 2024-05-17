<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.4.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 * @brief      EXTERNAL LINK ID (get d3comment_info also)
 */

$query4assign = [];

if ( ! empty( $forum_row['forum_external_link_format'] ) && ! empty( $_GET['external_link_id'] ) ) {

	$external_link_id                 = $myts->stripSlashesGPC( $_GET['external_link_id'] );
	$query4assign['external_link_id'] = htmlspecialchars( $external_link_id, ENT_QUOTES );
	$query4nav                        .= '&amp;external_link_id=' . rawurlencode( $external_link_id );
	$external_link_id4sql             = addslashes( $external_link_id );
	$whr_external_link_id             = "t.topic_external_link_id='$external_link_id4sql'";

	$d3comment_info = [
		'comment_link'        => d3forum_get_comment_link( $forum_row['forum_external_link_format'], $external_link_id ),
		'comment_description' => d3forum_get_comment_description( $mydirname, $forum_row['forum_external_link_format'], $external_link_id, $forum_id ?? null ),
	];

	if ( is_array( $d3comment_info['comment_description'] ) ) {
		$xoops_breadcrumbs[] = [
			'name' => $d3comment_info['comment_description']['subject'],
		];
	}
} else {
	unset( $external_link_id );
	$query4assign['external_link_id'] = '';
	$whr_external_link_id             = '1';
	$d3comment_info                   = [];
}

// TXT
if ( ! empty( $_GET['txt'] ) ) {
	$txt                 = $myts->stripSlashesGPC( $_GET['txt'] );
	$query4assign['txt'] = htmlspecialchars( $txt, ENT_QUOTES );
	$query4nav           .= '&amp;txt=' . rawurlencode( $txt );
	$txt4sql             = addslashes( $txt );
	$whr_txt             = "fp.subject LIKE '%$txt4sql%' OR fp.post_text LIKE '%$txt4sql%'";
} else {
	$query4assign['txt'] = '';
	$whr_txt             = '1';
}

// SOLVED
$solved_options = [
	0 => '----',
	1 => _MD_D3FORUM_OPT_SOLVEDYES,
	2 => _MD_D3FORUM_OPT_SOLVEDNO,
];

$solved_sqls = [
	0 => '1',
	1 => 't.topic_solved=1',
	2 => 't.topic_solved=0',
];

if ( empty( $xoopsModuleConfig['use_solved'] ) ) {
	// disable "solved function"
	$query4assign['solved'] = 0;
	$whr_solved             = $solved_sqls[0];
} else if ( ! empty( $solved_options[ @$_GET['solved'] ] ) ) {
	$query4assign['solved'] = (int) $_GET['solved'];
	$query4nav              .= '&amp;solved=' . (int) $_GET['solved'];
	$whr_solved             = $solved_sqls[ $query4assign['solved'] ];
} else {
	$query4assign['solved'] = 0;
	$whr_solved             = $solved_sqls[0];
}

// ORDER
$odr_options = [
	1  => _MD_D3FORUM_ODR_LASTPOSTDSC,
	2  => _MD_D3FORUM_ODR_LASTPOSTASC,
	3  => _MD_D3FORUM_ODR_CREATETOPICDSC,
	4  => _MD_D3FORUM_ODR_CREATETOPICASC,
	5  => _MD_D3FORUM_ODR_REPLIESDSC,
	6  => _MD_D3FORUM_ODR_REPLIESASC,
	7  => _MD_D3FORUM_ODR_VIEWSDSC,
	8  => _MD_D3FORUM_ODR_VIEWSASC,
	9  => _MD_D3FORUM_ODR_VOTESDSC,
	10 => _MD_D3FORUM_ODR_VOTESASC,
	11 => _MD_D3FORUM_ODR_POINTSDSC,
	12 => _MD_D3FORUM_ODR_POINTSASC,
	13 => _MD_D3FORUM_ODR_AVGDSC,
	14 => _MD_D3FORUM_ODR_AVGASC,
];

$odr_sqls = [
	1  => 't.topic_last_post_time desc',
	2  => 't.topic_last_post_time',
	3  => 't.topic_first_post_time desc',
	4  => 't.topic_first_post_time',
	5  => 't.topic_posts_count desc',
	6  => 't.topic_posts_count',
	7  => 't.topic_views desc',
	8  => 't.topic_views',
	9  => 't.topic_votes_count desc',
	10 => 't.topic_votes_count',
	11 => 't.topic_votes_sum desc',
	12 => 't.topic_votes_sum',
	13 => 't.topic_votes_sum/(t.topic_votes_count+0.0001) desc',
	14 => 't.topic_votes_sum/(t.topic_votes_count+0.0001)',
];

if ( ! empty( $odr_options[ @$_GET['odr'] ] ) ) {
	$query4assign['odr'] = (int) $_GET['odr'];
	$query4nav           .= '&amp;odr=' . (int) $_GET['odr'];
	$odr_query           = $odr_sqls[ $query4assign['odr'] ];
} else {
	$query4assign['odr'] = 1;
	$odr_query           = 'u2t.u2t_marked <=> 1 DESC,t.topic_sticky DESC,' . $odr_sqls[1];
}

// POS
$pos = (int) @$_GET['pos'] <= 0 ? 0 : (int) $_GET['pos'];

// LIMIT
$num = $xoopsModuleConfig['topics_per_page'] < 5 ? 5 : (int) $xoopsModuleConfig['topics_per_page'];
