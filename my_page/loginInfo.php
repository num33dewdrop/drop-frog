<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『　ログイン情報編集ページ　『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
authLogin();
//DBからユーザーデータを取得
$dbFormData   = getUser( $_SESSION['user_id'] );
$dbPassData   = getUserPass( $_SESSION['user_id'] );
$passEdit_flg = false;

//POST送信されていた場合
if ( ! empty( $_POST ) ) {
	debug( 'POST情報：' . print_r( $_POST, true ) );
	//変数に情報を代入
	$pass_new = $_POST['pass_new'];
	$pass_re  = $_POST['pass_re'];
	$email    = $_POST['email'];
	$pass_old = $_POST['pass_old'];

	//未入力チェック
	validRequired( $email, 'email' );
	validRequired( $pass_old, 'pass_old' );

	if ( empty( $err_msg ) ) {
		//DBの情報と入力情報が異なる場合にemailチェック
		diffDb( $email, 'email', Closure::fromCallable( 'validTempEmail' ) );
		//古いパスワードとDBパスワードを照合
		//DBに入っているデータと同じであれば、半角英数字チェックや最大文字チェックは行わなくても問題ない
		if ( ! password_verify( $pass_old, $dbPassData['pass'] ) ) {
			$err_msg['pass_old'] = MSG_16;
		}
		//新しいパスワードに入力がある場合
		if ( empty( $err_msg ) && ! empty( $pass_new ) ) {
			//再入力の未入力チェック
			validRequired( $pass_re, 'pass_re' );
			if ( empty( $err_msg ) ) {
				//新しいパスワードチェック
				validTempPass( $pass_new, 'pass_new' );
				//パスワード再入力一致チェック
				validMatch( $pass_new, $pass_re, 'pass_re' );
				//古いパスワードと新しいパスワード一致チェック
				if ( $pass_old === $pass_new ) {
					$err_msg['pass_new'] = MSG_15;
				}
				//エラーがない場合パスワード変更用フラグ書き換え
				if ( empty( $err_msg ) ) {
					$passEdit_flg = true;
				}
			}
		}
		if ( empty( $err_msg ) ) {
			debug( 'バリデーションOK。' );
			try {
				//DB接続
				$dbh = dbConnect();
				//SQL文作成
				$sql = 'UPDATE users SET email = :email';
				//新しいパスワード入力有無でSQL文変更
				switch ( $passEdit_flg ) {
					case true:
						$sql  = $sql . ', pass = :pass WHERE id = :u_id';
						$data = array(
							':email' => $email,
							':pass'  => password_hash( $pass_new, PASSWORD_DEFAULT ),
							':u_id'  => $dbFormData['id']
						);
						break;
					default:
						$sql  = $sql . ' WHERE id = :u_id';
						$data = array( ':email' => $email, ':u_id' => $dbFormData['id'] );
				}
				//クエリ実行
				$stmt = queryPost( $dbh, $sql, $data );
				if ( dbAffect( $stmt ) ) {
					$_SESSION['msg_success'] = SUC_01;
					//メールを送信
					$username = ( $dbFormData['nick_name'] ) ?: 'no name';

					$to      = $dbFormData['email'];
					$subject = '【パスワード変更通知】｜DROP FROG';
					//EOTはEndOfTextの略。ABCでもなんでもいい。先頭の<<<の後の文字列と合わせること。最後のEOTの前後に空白など何も入れてはいけない。
					//EOT内の半角空白も全てそのまま半角空白として扱われるのでインデントはしないこと
					$comment = <<<EOT
{$username}　さん
パスワードが変更されました。
                      
////////////////////////////////////////
DROP FROGカスタマーセンター
URL  https://drop-frog.com
E-mail info@drop-frog.com
////////////////////////////////////////
EOT;
					sendMail( $to, $subject, $comment );
					debug( 'マイページへ遷移します。' );
					header( "Location:" . MY_PAGE );
					exit();
				}
			} catch ( PDOException $e ) {
				error_log( 'エラー発生：' . $e->getMessage() );
				$err_msg['common'] = MSG_07;
			}
		}
	}
}
debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );

$title = 'ログイン情報編集';
require HEAD;
require HEADER;
?>
    <main id="main" class="l-main">
        <section class="c-container">
            <h2 class="c-subTitle c-subTitle--col2">マイページ</h2>
            <div class="c-container__col2">
				<?php require SIDEBAR; ?>
                <div class="c-container__main">
                    <div class="c-container__body c-container__body--middleSize">
                        <h3 class="c-thrTitle c-thrTitle--center">ログイン情報・編集</h3>
                        <form action="" method="post" class="c-editForm" enctype="multipart/form-data">
                            <div class="c-areaMsg c-areaMsg--common">
								<?php echo getErrMsg( 'common' ); ?>
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="pass_new" class="<?php echo getErrClass( 'pass_new' ); ?>">
                                    新しいPassword
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'pass_new' ); ?></span>
                                </label>
                                <input type="password" id="pass_new" class="c-editForm__input" name="pass_new"
                                       value="<?php echo getMethodHold( 'pass_new' ); ?>" autocomplete="off">
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="pass_re" class="<?php echo getErrClass( 'pass_re' ); ?>">
                                    新しいPassword 再入力
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'pass_re' ); ?></span>
                                </label>
                                <input type="password" id="pass_re" class="c-editForm__input" name="pass_re"
                                       value="<?php echo getMethodHold( 'pass_re' ); ?>" autocomplete="off">
                            </div>
                            <p class="u-marginB-xl u-fontSize-s">※Passwordを変更する場合のみ入力して下さい</p>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="email" class="<?php echo getErrClass( 'email' ); ?> ">
                                    E-mail
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'email' ); ?></span>
                                </label>
                                <input type="text" id="email" class="c-editForm__input" name="email"
                                       value="<?php echo getDataAndHold( 'email' ); ?>">
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="pass_old" class="<?php echo getErrClass( 'pass_old' ); ?> ">
                                    現在のPassword
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'pass_old' ); ?></span>
                                </label>
                                <input type="password" id="pass_old" class="c-editForm__input" name="pass_old"
                                       value="<?php echo getMethodHold( 'pass_old' ); ?>" autocomplete="off">
                            </div>
                            <div class="c-flex c-flex--justCenter u-paddingT-l">
                                <input type="submit" value="登録する" class="c-btn" name="register">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?php
require FOOTER;
?>