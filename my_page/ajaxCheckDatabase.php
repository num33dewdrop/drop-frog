<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『　AJAXデータベース監視処理ページ　『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

// postがあり、ユーザーIDがあり、ログインしている場合
if ( ! empty( $_POST['p_id'] ) && isset( $_SESSION['user_id'] ) && isLogin() ) {
	debug( 'POST送信があります。' );
	$p_id = $_POST['p_id'];

	// 前回の最新挿入日時をセッションから取得
	$lastInsertTime = ! empty( $_SESSION['lastInsertTime'] ) ? $_SESSION['lastInsertTime'] : $_SESSION['pageOpenTime'];
	// データベースをチェックして新しいレコードが挿入されたかどうかを返す処理
	try {
		$dbh    = dbConnect();
		$sql    = 'SELECT m.msg, m.update_date, u.pic, u.nick_name
				FROM msg AS m
				INNER JOIN users AS u ON m.from_user_id = u.id
				WHERE m.product_id = :p_id AND m.from_user_id != :id AND m.update_date > :update_date AND m.delete_flg = 0';
		$data   = array( ':p_id' => $p_id, ':id' => $_SESSION['user_id'], ':update_date' => $lastInsertTime );
		$stmt   = queryPost( $dbh, $sql, $data );
		$result = $stmt ? $stmt->fetchAll() : false;

		if ( ! empty( $result ) && end( $result )['update_date'] !== $lastInsertTime ) {
			// 最新の挿入日時をセッションに保存
			$_SESSION['lastInsertTime'] = end( $result )['update_date'];

			// レスポンスをJSON形式で返す
			echo json_encode( $result );
		}
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
	}
}
debug( 'Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );