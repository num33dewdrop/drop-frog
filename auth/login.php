<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『『　ログインページ　『『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
authLogin();
//post送信されていた場合
if ( ! empty( $_POST ) ) {
	//ユーザー情報を変数に代入
	$email     = $_POST['email'];                //必須
	$pass      = $_POST['pass'];                  //必須
	$pass_save = ! empty( $_POST['pass_save'] );//真偽値をそのまま代入

	//未入力
	validRequired( $email, 'email' );
	validRequired( $pass, 'pass' );
	if ( empty( $err_msg ) ) {
		//Email形式
		validEmail( $email, 'email' );
		//最大文字数
		validMaxLen( $email, 'email' );
		//パスワードチェック
		validTempPass( $pass, 'pass' );

		if ( empty( $err_msg ) ) {
			debug( 'バリデーションOKです。' );
			try {
				//DB接続
				$dbh = dbConnect();
				//SQL文作成
				$sql  = 'SELECT pass, id FROM users WHERE email = :email AND delete_flg = 0';
				$data = array( ':email' => $email );
				//SQL実行
				$stmt = queryPost( $dbh, $sql, $data );
				//結果の取得
				$result = $stmt->fetch( PDO::FETCH_ASSOC );
				//結果の取得ができており（Emailが違うと取得できない）、かつ、結果が入力パスワードとマッチしていた場合
				if ( ! empty( $result ) && password_verify( $pass, array_shift( $result ) ) ) {
					debug( 'パスワードがマッチしました' );
					//ログイン有効期限（デフォルトを１時間とする）
					$sesLimit = 60 * 60;//1時間とする
					//最終ログイン日時を現在日時に
					$_SESSION['login_date'] = time();
					if ( $pass_save ) {
						debug( 'ログイン保持にチェックがあります。' );
						//ログイン有効期限を15日とする
						$_SESSION['login_limit'] = $sesLimit * 24 * 15;
					} else {
						debug( 'ログイン保持にチェックはありません。' );
						//ログイン有効期限を1時間とする
						$_SESSION['login_limit'] = $sesLimit;
					}
					//ユーザーID格納
					$_SESSION['user_id'] = $result['id'];
					debug( 'セッション変数の中身：' . print_r( $_SESSION, true ) );
					if ( ! empty( $_SESSION['login_key'] ) ) {
						debug( '元いたページへ遷移' );
						header( 'Location:' . $_SESSION['login_key'] );
						unset( $_SESSION['login_key'] );
						exit();
					} else {
						debug( 'マイページへ遷移します' );
						header( 'Location:' . MY_PAGE );
						exit();
					}
					//メールアドレスまたはパスワードが違う場合
				} else {
					debug( 'メールアドレスまたはパスワードが違います' );
					$err_msg['common'] = MSG_09;
				}
			} catch ( PDOException $e ) {
				debug( 'catch' );
				error_log( 'エラー発生：' . $e->getMessage() );
				$err_msg['common'] = MSG_07;
			}
		}
	}
}
debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );

$title = 'ログイン';
require HEAD;
require HEADER;
?>
    <main id="main" class="l-main">
        <section class="c-container">
            <h2 class="c-subTitle">ログイン</h2>
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
                <div class="c-flex c-flex--alignCen c-flex--spaceBet">
                    <div class="c-authForm__checkArea">
                        <div class="u-marginB-s c-check">
                            <input class="c-check__box" id="check" type="checkbox"
                                   name="pass_save" <?php getMethodHold( 'pass_save', true ); ?>>
                            <label for="check"></label>
                            <span>自動でログインする</span>
                        </div>
                        <span>パスワードを忘れた場合は<a href="<?php echo PASS_REISSUE_SEND ?>">こちら</a></span>
                    </div>
                    <input type="submit" name="login" class="c-btn" value="ログイン">
                </div>
            </form>
        </section>
    </main>
<?php
require FOOTER;
?>