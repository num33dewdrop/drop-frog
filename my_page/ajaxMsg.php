<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『　AJAXメッセージ処理ページ　『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

// postがあり、ユーザーIDがあり、ログインしている場合
if ( ! empty( $_POST ) && isset( $_SESSION['user_id'] ) && isLogin() ) {
	debug( 'POST送信があります。' );
	$p_id     = isset( $_POST['p_id'] ) ? $_POST['p_id'] : '';
	$send_msg = ! empty( $_POST['send_msg'][0]['value'] ) ? $_POST['send_msg'][0]['value'] : '';

	$myInfo = getUser( $_SESSION['user_id'] );

	//バリーデーション
	validRequired( $send_msg, 'send_msg' );
	validMaxLen( $send_msg, 'send_msg', 500 );

	if ( empty( $err_msg ) ) {
		debug( 'バリデーションOK' );
		$date = date( 'Y-m-d H:i:s' );
		try {
			//DB接続
			$dbh = dbConnect();
			//SQL文作成
			$sql  = 'INSERT INTO msg(product_id, from_user_id, msg, create_date) VALUES(:p_id, :from_u_id, :msg, :create_date)';
			$data = array(
				':p_id'        => $p_id,
				':from_u_id'   => $_SESSION['user_id'],
				':msg'         => $send_msg,
				':create_date' => $date
			);
			//クエリ実行
			$stmt = queryPost( $dbh, $sql, $data );
			if ( dbAffect( $stmt ) ) {
				echo json_encode( array(
					'send_msg' => $send_msg,
					'date'     => $date,
					'pic'      => $myInfo['pic']
				) );
			}
		} catch ( PDOException $e ) {
			error_log( 'エラー発生：' . $e->getMessage() );
		}
	}
}
debug( 'Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );