<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『『　ご意見箱ページ　『『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

if ( ! empty( $_POST ) ) {
	debug( 'POST情報：' . print_r( $_POST, true ) );
	$msg = $_POST['opinion'];
	validRequired( $msg, 'opinion' );
	validMaxLen( $msg, 'opinion' );
	if ( empty( $err_msg ) ) {
		try {
			$dbh  = dbConnect();
			$sql  = 'INSERT INTO opinion_box(msg, create_date) VALUES (:msg, :create_date)';
			$data = array( ':msg' => $msg, ':create_date' => date( 'Y-m-d H:i:s' ) );
			$stmt = queryPost( $dbh, $sql, $data );
			if ( dbAffect( $stmt ) ) {
				$_SESSION['msg_success'] = SUC_10;
				header( 'Location:' . $_SERVER['HTTP_REFERER'] );
			}
		} catch ( PDOException $e ) {
			error_log( 'エラー発生：' . $e->getMessage() );
			$_SESSION['msg_err'] = MSG_07;
			header( 'Location:' . $_SERVER['HTTP_REFERER'] );
		}
	} else {
		$_SESSION['msg_err'] = $err_msg['opinion'];
		header( 'Location:' . $_SERVER['HTTP_REFERER'] );
	}
} else {
	error_log( 'エラー発生:パラメータが不正です' );
	header( 'Location:' . HOME );
}

debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );