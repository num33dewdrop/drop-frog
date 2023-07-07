<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『　AJAXお気に入り処理ページ　『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

// postがあり、ユーザーIDがあり、ログインしている場合
if ( isset( $_POST['p_id'] ) && isset( $_SESSION['user_id'] ) && isLogin() ) {
	debug( 'POST送信があります。' );
	$p_id = $_POST['p_id'];
	//例外処理
	try {
		// DBへ接続
		$dbh = dbConnect();
		// レコードがあるか検索
		$sql   = 'SELECT count(*) FROM favorite WHERE product_id = :p_id AND customer_user_id = :u_id';
		$data1 = array( ':u_id' => $_SESSION['user_id'], ':p_id' => $p_id );
		// クエリ実行
		$stmt   = queryPost( $dbh, $sql, $data1 );
		$result = $stmt->fetchColumn();
		debug( $result );
		// レコードが１件でもある場合
		if ( ! empty( $result ) ) {
			// レコードを削除する
			$sql1 = 'DELETE FROM favorite WHERE product_id = :p_id AND customer_user_id = :u_id';
		} else {
			// レコードを挿入する
			$sql1  = 'INSERT INTO favorite (product_id, customer_user_id, create_date) VALUES (:p_id, :u_id, :create_date)';
			$data1 = array(
				':p_id'        => $p_id,
				':u_id'        => $_SESSION['user_id'],
				':create_date' => date( 'Y-m-d H:i:s' )
			);
			// お知らせに情報を挿入
			$sql2  = 'INSERT INTO p_notice (product_id, customer_user_id, notice_type, create_date) VALUES (:p_id, :u_id, :notice_type, :create_date)';
			$data2 = array_merge( $data1, array( 'notice_type' => 'favorite' ) );
		}
		// クエリ実行
		$stmt1 = queryPost( $dbh, $sql1, $data1 );
		if ( dbAffect( $stmt1 ) ) {
			// クエリ実行
			if ( ! empty( $sql2 ) && ! empty( $data2 ) ) {
				$stmt2 = queryPost( $dbh, $sql2, $data2 );
				dbAffect( $stmt2 );
			}
			//表示用総カウントデータ
			$favoriteNum = getFavoriteNum( $p_id );
			debug( '$favoriteNum : ' . $favoriteNum );
			echo json_encode( (int) $favoriteNum );
		}
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
	}
} else {
	debug( '$_SERVER :' . print_r( $_SERVER, true ) );
	$_SESSION['login_key'] = $_SERVER['HTTP_REFERER'];
}
debug( 'Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );