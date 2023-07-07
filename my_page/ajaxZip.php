<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『　AJAX郵便番号処理ページ　『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

// postがあり、ユーザーIDがあり、ログインしている場合
if ( ! empty( $_POST['zip'] ) && isset( $_SESSION['user_id'] ) && isLogin() ) {
	debug( 'POST送信があります。' );
	//jsから受け取ったデータ
	$zip = $_POST['zip'];
	//郵便番号を元にAPIからデータを取得
	$resItem = get_zipAddress( $zip, true );
	if ( $resItem ) {
		echo $resItem;
	}
}
debug( 'Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );