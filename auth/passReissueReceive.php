<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『　認証キー入力ページ　『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
//セッションに認証キーがない場合、認証キー送信ページへ遷移
if ( empty( $_SESSION['auth_key'] ) ) {
	header( 'Location:' . PASS_REISSUE_SEND );
	exit();
}

//POST送信されていた場合
if ( ! empty( $_POST ) ) {
	debug( 'POST情報：' . print_r( $_POST, true ) );
	$auth_key = $_POST['token'];
	//未入力
	validRequired( $auth_key, 'token' );
	if ( empty( $err_msg ) ) {
		//固定長
		validFixedLen( $auth_key, 'token' );
		//半角英数字
		validHalf( $auth_key, 'token' );
		if ( empty( $err_msg ) ) {
			//POSTのキーとセッションのキーが同じか
			if ( $auth_key !== $_SESSION['auth_key'] ) {
				$err_msg['common'] = MSG_17;
			}
			//セッションのキーは有効期限内か
			if ( $_SESSION['auth_key_limit'] < time() ) {
				$err_msg['common'] = MSG_18;
			}

			if ( empty( $err_msg ) ) {
				debug( 'バリデーションOK' );
				//パスワード生成
				$pass = mkRandKey();
				debug( '新しいパスワード' . $pass );
				try {
					//DB接続
					$dbh = dbConnect();
					//SQL文作成
					$sql  = 'UPDATE users SET pass = :pass WHERE email = :email AND delete_flg = 0';
					$data = array(
						':email' => $_SESSION['auth_email'],
						':pass'  => password_hash( $pass, PASSWORD_DEFAULT )
					);
					//クエリ実行
					$stmt = queryPost( $dbh, $sql, $data );
					debug( '$stmt : ' . print_r( $stmt, true ) );
					//UPDATE,DELETE文では影響を受けた行数が0の場合はクエリの実行に失敗したと判断
					//正常に実行されたSQL文が実際にデータベースに影響を与えたかどうかは、
					//rowCount()メソッドを使って確認する必要がある
					if ( dbAffect( $stmt ) ) {
						//メール送信
						$to      = $_SESSION['auth_email'];
						$subject = '【パスワード再発行完了】｜DROP FROG';
						//EOTはEndOfTextの略。ABCでもなんでもいい。先頭の<<<の後の文字列と合わせること。最後のEOTの前後に空白など何も入れてはいけない。
						//EOT内の半角空白も全てそのまま半角空白として扱われるのでインデントはしないこと
						$comment = <<<EOT
本メールアドレス宛にパスワードの再発行を致しました。
下記のURLにて再発行パスワードをご入力頂き、ログインください。

ログインページ：https://drop-frog.com/auth/login.php
再発行パスワード：{$pass}
※ログイン後、パスワードのご変更をお願い致します

////////////////////////////////////////
DROP FROGカスタマーセンター
URL  https://drop-frog.com
E-mail info@drop-frog.com
////////////////////////////////////////
EOT;
						sendMail( $to, $subject, $comment );
						//セッション削除
						//中身を削除し、セッションIDはそのまま
						session_unset();
						//セッションにメッセージを格納
						$_SESSION['msg_success'] = SUC_03;
						//ログインページへ遷移
						header( 'Location:' . LOGIN );
						exit();
					}
				} catch ( PDOException $e ) {
					error_log( 'エラー発生：' . $e->getMessage() );
					$err_msg['common'] = MSG_07;
				}
			}
		}
	}
}
debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );

$title = '認証キー入力';
require HEAD;
require HEADER;
?>
<main id="main" class="l-main">
    <section class="c-container">
        <h2 class="c-subTitle">認証キー入力</h2>
        <form action="" method="post" class="c-authForm">
            <p class="u-fontSize-s u-marginB-l">
                ご指定のメールアドレス宛にお送りした【パスワード再発行認証】のメール内にある<br>
                「認証キー」を入力してください。<br>
                ご指定のメールアドレス宛に再発行されたパスワードをお送り致します。<br>
                再発行されたパスワードでログインした後、速やかにパスワードの再設定をお願い致します。
            </p>
            <div class="c-areaMsg c-areaMsg--common">
				<?php echo getErrMsg( 'common' ); ?>
            </div>
            <label for="token" class="<?php echo getErrClass( 'token' ); ?>">
                認証キー
                <span class="c-areaMsg"><?php echo getErrMsg( 'token' ); ?></span>
            </label>
            <input class="c-authForm__input" id="token" type="password" name="token"
                   value="<?php echo getMethodHold( 'token' ); ?>" autocomplete="off">
            <input type="submit" class="c-btn c-btn--right" value="送信する">
        </form>
    </section>
</main>
<?php
require FOOTER;
?>
