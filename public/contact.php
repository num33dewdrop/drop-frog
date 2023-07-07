<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『　お問い合わせページ　『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

//post送信されていた場合
if ( ! empty( $_POST ) ) {
	if ( ! empty( $_SESSION['user_id'] ) ) {
		$contact_msg = $_POST['contact_msg'];
		validRequired( $contact_msg, 'contact_msg' );
		validMaxLen( $contact_msg, 'contact_msg' );

		if ( empty( $err_msg ) ) {
			debug( 'バリデーションOKです。' );
			try {
				$dbh  = dbConnect();
				$sql  = 'INSERT INTO contact_registrant(from_user_id, msg, create_date) VALUES(:u_id , :msg, :create_date)';
				$data = array(
					':u_id'        => $_SESSION['user_id'],
					':msg'         => $contact_msg,
					':create_date' => date( 'Y-m-d H:i:s' )
				);
				$stmt = queryPost( $dbh, $sql, $data );
				// クエリ成功の場合
				if ( dbAffect( $stmt ) ) {
					$_SESSION['msg_success'] = SUC_11;
					header( 'Location:' . HOME );
					exit();
				}
			} catch ( PDOException $e ) {
				error_log( 'エラー発生：' . $e->getMessage() );
				$err_msg['common'] = MSG_07;
			}
		}
	} else {
		//変数にユーザー情報を代入
		$name        = $_POST['name'];
		$email       = $_POST['email'];
		$contact_msg = $_POST['contact_msg'];
		validRequired( $name, 'name' );
		validRequired( $email, 'email' );
		validRequired( $contact_msg, 'contact_msg' );
		if ( empty( $err_msg ) ) {
			validMaxLen( $name, 'name' );
			validMaxLen( $email, 'email' );
			validMaxLen( $contact_msg, 'contact_msg' );

			validEmail( $email, 'email' );
			if ( empty( $err_msg ) ) {
				debug( 'バリデーションOKです。' );
				try {
					$dbh  = dbConnect();
					$sql  = 'INSERT INTO contact_general(name, email, msg, create_date) VALUES(:name ,:email, :msg, :create_date)';
					$data = array(
						':name'        => $name,
						':email'       => $email,
						':msg'         => $contact_msg,
						':create_date' => date( 'Y-m-d H:i:s' )
					);
					$stmt = queryPost( $dbh, $sql, $data );
					// クエリ成功の場合
					if ( dbAffect( $stmt ) ) {
						$_SESSION['msg_success'] = SUC_11;
						header( 'Location:' . HOME );
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
$title = 'お問い合わせ';
require HEAD;
require HEADER;
?>
<main id="main" class="l-main">
    <section class="c-container">
        <div class="c-fixedPage">
            <h2 class="c-fixedPage__title c-fixedPage__title--main">お問い合わせ</h2>
            <form action="" method="post" class="c-fixedPage__form">
                <div class="c-areaMsg c-areaMsg--common">
					<?php echo getErrMsg( 'common' ); ?>
                </div>
				<?php if ( empty( $_SESSION['user_id'] ) ) { ?>
                    <label class="<?php echo getErrClass( 'name' ); ?>" for="name">
                        お名前
                        <span class="c-areaMsg"><?php echo getErrMsg( 'name' ); ?></span>
                    </label>
                    <input class="c-fixedPage__input" id="name" type="text" name="name"
                           value="<?php echo getMethodHold( 'name' ); ?>">
                    <label class="<?php echo getErrClass( 'email' ); ?>" for="email">
                        email
                        <span class="c-areaMsg"><?php echo getErrMsg( 'email' ); ?></span>
                    </label>
                    <input class="c-fixedPage__input" id="email" type="text" name="email"
                           value="<?php echo getMethodHold( 'email' ); ?>">
				<?php } ?>
                <label class="<?php echo getErrClass( 'contact_msg' ); ?>" for="contact_msg">
                    お問い合わせ内容
                    <span class="c-areaMsg"><?php echo getErrMsg( 'contact_msg' ); ?></span>
                </label>
                <textarea class="c-fixedPage__input c-fixedPage__area" name="contact_msg" id="contact_msg" cols="30"
                          rows="10"><?php echo getMethodHold( 'contact_msg' ); ?></textarea>
                <input type="submit" class="c-btn c-btn--right" value="送信する">
            </form>
        </div>
    </section>
</main>
<?php
require FOOTER;
?>
