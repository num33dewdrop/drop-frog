<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『　パスワード再発行送信ページ　『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

//POST送信されていた場合
if ( ! empty( $_POST ) ) {
	debug( 'POST情報：' . print_r( $_POST, true ) );
	$email = $_POST['email'];
	//未入力
	validRequired( $email, 'email' );
	if ( empty( $err_msg ) ) {
		//email形式
		validEmail( $email, 'email' );
		//最大文字数
		validMaxLen( $email, 'email' );

		if ( empty( $err_msg ) ) {
			debug( 'バリデーションOK。' );
			try {
				$dbh    = dbConnect();
				$sql    = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
				$data   = array( ':email' => $email );
				$stmt   = queryPost( $dbh, $sql, $data );
				$result = $stmt->fetch( PDO::FETCH_ASSOC );
				debug( '$result : ' . print_r( $result, true ) );
				//結果の取得ができており、かつDBに登録されている場合
				if ( ! empty( $result ) && array_shift( $result ) ) {
					debug( 'DB登録あり。' );
					$_SESSION['msg_success'] = SUC_03;

					//認証キー生成
					$auth_key = mkRandKey();
					debug( $auth_key );
					//メール送信
					$to      = $email;
					$subject = '【パスワード再発行認証】｜DROP FROG';
					//EOTはEndOfTextの略。ABCでもなんでもいい。先頭の<<<の後の文字列と合わせること。最後のEOTの前後に空白など何も入れてはいけない。
					//EOT内の半角空白も全てそのまま半角空白として扱われるのでインデントはしないこと
					$comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：https://drop-frog.com/auth/passReissueRecieve.php
認証キー：{$auth_key}
※認証キーの有効期限は30分となります

認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
https://drop-frog/auth/passReissueSend.php

////////////////////////////////////////
DROP FROGカスタマーセンター
URL  https://drop-frog.com
E-mail info@drop-frog.com
////////////////////////////////////////
EOT;
					sendMail( $to, $subject, $comment );

					//認証に必要な情報をセッションに保存
					$_SESSION['auth_key']   = $auth_key;
					$_SESSION['auth_email'] = $email;
					//現在時刻より30分後のUNIXタイムスタンプを入れる
					$_SESSION['auth_key_limit'] = time() + ( 60 * 30 );
					debug( 'セッション変数の中身：' . print_r( $_SESSION, true ) );
					//ログインページへ遷移
					header( 'Location:' . PASS_REISSUE_RECEIVE );
					exit();
				} else {
					debug( 'クエリに失敗したかDBに登録のないEmailが入力されました。' );
					$err_msg['common'] = MSG_07;
				}
			} catch ( PDOException $e ) {
				error_log( 'エラー発生：' . $e->getMessage() );
				$err_msg['common'] = MSG_07;
			}
		}
	}
}

debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );

$title = 'パスワード再発行送信';
require HEAD;
require HEADER;
?>
<main id="main" class="l-main">
    <section class="c-container">
        <h2 class="c-subTitle">パスワード再発行送信</h2>
        <form action="" method="post" class="c-authForm">
            <p class="u-fontSize-s u-marginB-l">ご指定のメールアドレスあてにパスワード再発行用のURLと認証キーをお送り致します。</p>
            <div class="c-areaMsg c-areaMsg--common">
				<?php echo getErrMsg( 'common' ); ?>
            </div>
            <label for="email" class="<?php echo getErrClass( 'email' ); ?>">
                email
                <span class="c-areaMsg"><?php echo getErrMsg( 'email' ); ?></span>
            </label>
            <input class="c-authForm__input" id="email" type="text" name="email"
                   value="<?php echo getMethodHold( 'email' ); ?>">
            <input type="submit" class="c-btn c-btn--right" value="送信する">
        </form>
    </section>
</main>
<?php
require FOOTER;
?>
