<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『　Ajaxテンプレートデータ取得ページ　『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();


// 画面表示用データ取得
//================================
if ( ! empty( $_POST ) && isset( $_SESSION['user_id'] ) && isLogin() ) {
	debug( 'POST送信があります。' );
	debug( 'POST情報：' . print_r( $_POST, true ) );

	$s_id           = $_POST['s_id'] ?? '';
	$currentPageNum = ! empty( $_POST['currentPageNum'] ) ? $_POST['currentPageNum'] : 1;
	$sort           = $_POST['sort'] ?? 0;
	$p_id           = $_POST['p_id'] ?? '';
	$sortData       = array();
	$dbData         = array();
	$sale_flg       = false;
	//作品表示件
	$listSpan = 12;
	//現在の表示レコード先頭を算出
	$currentMinNum = ( $currentPageNum - 1 ) * $listSpan;

	// DBから作品データを取得
	switch ( $s_id ) {
		case 'favoriteProduct':
			debug( 'いいね一覧データを取得します' );
			$dbData   = getMyLike( $_SESSION['user_id'], $sort, $currentMinNum );
			$sortData = $sortFData;
			break;
		case 'browsingProduct':
			debug( '閲覧履歴データを取得します' );
			$dbData   = getMyBrowsingProduct( $_SESSION['user_id'], $sort, $currentMinNum );
			$sortData = $sortHData;
			break;
		case 'board':
			//掲示板一覧
			debug( '掲示板データを取得します' );
			$dbData   = getMySendBoards( $_SESSION['user_id'], $sort, $currentMinNum );
			$sortData = $sortBData;
			break;
		case 'draftProduct':
			//下書き一覧
			debug( '下書きデータを取得します' );
			$dbData   = getMyDrafts( $_SESSION['user_id'], $sort, $currentMinNum );
			$sortData = $sortRData;
			break;
		case 'saleProduct':
			//投稿作品
			debug( '投稿作品データを取得します' );
			$dbData   = getMyProducts( $_SESSION['user_id'], $sort, $currentMinNum );
			$sortData = $sortRData;
			break;
	}
	debug( '$dbData : ' . print_r( $dbData, true ) );
	//作品データがある場合
	if ( ! empty( $dbData['data'] ) ) {
		foreach ( $dbData['data'] as $key => $val ) {
			//取得したデータ全てをサニタイズする
			$val = array_map( 'sanitize', $val );
			//画像処理
			if ( array_key_exists( 'pic1', $val ) ) {
				$val['pic1'] = showImg( $val['pic1'] );
			}
			//メッセージ件数取得
			$msgNum = sanitize( getMsgNum( $val['p_id'] ) );
			//userPage用簡易ハッシュ化
			$hashId = ! empty( $val['hash'] ) ?
				'?sh=' . hash( "sha256", $val['user_id'] . $val['hash'] ) : '';

			//いいね数
			$favoriteNum = sanitize( getFavoriteNum( $val['p_id'] ) );
			//いいねフラグ
			$favorite_flg = isLike( $_SESSION['user_id'], $val['p_id'] );
			//レンダリング用のデータをマージ
			$val = array_merge( $val, array(
				'msgNum'       => $msgNum,
				'hashId'       => $hashId,
				'favoriteNum'  => $favoriteNum,
				'favorite_flg' => $favorite_flg
			) );
			//レンダリングしないデータを排除
			unset( $val['user_id'], $val['hash'] );
			//反映
			$dbData['data'][ $key ] = $val;
		}
		$pagination = pagination2( $currentPageNum, $dbData['total_page'] );
	}
	debug( '$dbData : ' . print_r( $dbData, true ) );
	// レスポンスをJSON形式で返す
	header( 'Content-Type: application/json' );
	echo json_encode( array(
		'dbData'        => ! empty( $dbData['data'] ) ? $dbData['data'] : '',
		'currentMinNum' => $currentMinNum,
		'total'         => ! empty( $dbData['total'] ) ? $dbData['total'] : 0,
		'sortData'      => $sortData,
		'pagination'    => ! empty( $pagination ) ? $pagination : ''
	) );
}


debug( 'Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );