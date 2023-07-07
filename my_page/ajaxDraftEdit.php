<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『　Ajax下書きデータ更新・削除ページ『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

// postがあり、ユーザーIDがあり、ログインしている場合
if ( ! empty( $_POST ) && isset( $_SESSION['user_id'] ) && isLogin() ) {
	debug( 'POST情報　：' . print_r( $_POST, true ) );
	if ( isset( $_POST['edit_p_id'] ) ) {
		$p_id = $_POST['edit_p_id'];
		publicProduct( $p_id, $_SESSION['user_id'] );
	}
	if ( isset( $_POST['delete_p_id'] ) ) {
		$p_id = $_POST['delete_p_id'];
		deleteProduct( $p_id, $_SESSION['user_id'] );
	}
}

debug( 'Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );