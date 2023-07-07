<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『　ユーザー登録ページ　『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
//post送信されていた場合
if ( ! empty( $_POST ) ) {
	//変数にユーザー情報を代入
	$email   = $_POST['email'];
	$pass    = $_POST['pass'];
	$pass_re = $_POST['pass_re'];

	//未入力チェック
	validRequired( $email, 'email' );
	validRequired( $pass, 'pass' );
	validRequired( $pass_re, 'pass_re' );

	if ( empty( $err_msg ) ) {
		//emailチェック
		validTempEmail( $email, 'email' );
		//パスワードチェック
		validTempPass( $pass, 'pass' );

		if ( empty( $err_msg ) ) {
			//パスワード再入力一致チェック
			validMatch( $pass, $pass_re, 'pass_re' );

			if ( empty( $err_msg ) ) {
				debug( 'バリデーションOKです。' );
				try {
					$dbh  = dbConnect();
					$sql  = 'INSERT INTO users(email, pass, login_time, create_date) VALUES(:email, :pass, :login_time, :create_date)';
					$data = array(
						':email'       => $email,
						':pass'        => password_hash( $pass, PASSWORD_DEFAULT ),
						':login_time'  => date( 'Y-m-d H:i:s' ),
						':create_date' => date( 'Y-m-d H:i:s' )
					);
					$stmt = queryPost( $dbh, $sql, $data );
					// クエリ成功の場合
					if ( dbAffect( $stmt ) ) {
						//ログイン有効期限（デフォルトを１時間とする）
						$sesLimit = 60 * 60;
						//最終ログイン日時を現在日時に
						$_SESSION['login_date']  = time();
						$_SESSION['login_limit'] = $sesLimit;
						// ユーザーIDを格納
						$_SESSION['user_id'] = $dbh->lastInsertId();
						debug( 'セッション変数の中身：' . print_r( $_SESSION, true ) );
                        $_SESSION['msg_success'] = SUC_06;
						header( "Location:" . MY_PAGE );//マイページへ
						exit();
					}
				} catch ( PDOException $e ) {
					error_log( 'エラー発生：' . $e->getMessage() );
					$err_msg = MSG_07;
				}
			}
		}
	}
}
debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );

$title = '新規登録';
require HEAD;
require HEADER;
?>
<main id="main" class="l-main">
    <section class="c-container">
        <h2 class="c-subTitle">新規登録</h2>
        <form action="" method="post" class="c-authForm">
            <div class="c-areaMsg c-areaMsg--common">
				<?php echo getErrMsg( 'common' ); ?>
            </div>
            <label class="<?php echo getErrClass( 'email' ); ?>" for="email">
                email
                <span class="c-areaMsg"><?php echo getErrMsg( 'email' ); ?></span>
            </label>
            <input class="c-authForm__input" id="email" type="text" name="email"
                   value="<?php echo getMethodHold( 'email' ); ?>">
            <label class="<?php echo getErrClass( 'pass' ); ?>" for="pass">
                Password
                <span class="c-areaMsg"><?php echo getErrMsg( 'pass' ); ?></span>
            </label>
            <input class="c-authForm__input" id="pass" type="password" name="pass"
                   value="<?php echo getMethodHold( 'pass' ); ?>" autocomplete="off">
            <label class="<?php echo getErrClass( 'pass_re' ); ?>" for="pass_re">
                Password 再入力
                <span class="c-areaMsg"><?php echo getErrMsg( 'pass_re' ); ?></span>
            </label>
            <input class="c-authForm__input" id="pass_re" type="password" name="pass_re"
                   value="<?php echo getMethodHold( 'pass_re' ); ?>" autocomplete="off">
            <div class="c-flex c-flex--flexEnd">
                <span class="u-fontSize-s u-marginR-m"><a
                            href="<?php echo TERMS_OF_USE; ?>">利用規約</a>に同意して登録する</span>
                <input type="submit" class="c-btn" value="登録する">
            </div>
        </form>
    </section>
</main>
<?php
require FOOTER;
?>
