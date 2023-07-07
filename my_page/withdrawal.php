<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『『『　退会ページ　『『『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
authLogin();
$withdrawalCategory = getWithdrawalCategory();
debug( '$reasonCategory：' . print_r( $withdrawalCategory, true ) );

//post送信されていた場合
if ( ! empty( $_POST ) ) {
	//変数に退会情報を代入
	$reason1 = $_POST['reason1'];
	$reason2 = getMethodHold( 'reason2' );
	//未入力
	validRequired( $reason1, 'reason1' );
	//最大文字数
	validMaxLen( $reason2, 'reason2', 500 );

	if ( empty( $err_msg ) ) {
		debug( 'バリデーションOK' );
		try {
			//DB接続
			$dbh = dbConnect();
			//SQL文作成
			$sql1  = 'UPDATE users SET delete_flg = 1 WHERE id = :u_id';
			$sql2  = 'UPDATE product SET delete_flg = 1 WHERE user_id = :u_id';
			$sql3  = 'UPDATE favorite SET delete_flg = 1 WHERE customer_user_id = :u_id';
			$data1 = array( ':u_id' => $_SESSION['user_id'] );
			//クエリ実行
			$stmt1 = queryPost( $dbh, $sql1, $data1 );
			$stmt2 = queryPost( $dbh, $sql2, $data1 );
			$stmt3 = queryPost( $dbh, $sql3, $data1 );
			//クエリ実行成功の場合（最悪userテーブルのみ削除成功していれば良しとする）
			if ( dbAffect( $stmt1 ) ) {
				//退会理由の保存
				$sql4  = 'INSERT INTO withdrawal(user_id, w_category_id, msg, create_date) VALUES(:u_id, :w_c_id, :msg, :create_date)';
				$data2 = array(
					':u_id'        => $_SESSION['user_id'],
					':w_c_id'      => $reason1,
					':msg'         => $reason2,
					':create_date' => date( 'Y-m-d H:i:s' )
				);
				$stmt4 = queryPost( $dbh, $sql4, $data2 );
				//セッションの削除
				session_destroy();
				debug( 'セッション変数の中身：' . print_r( $_SESSION, true ) );
				debug( 'トップページへ遷移します。' );
				header( 'Location:' . HOME );
				exit();
			}
		} catch ( PDOException $e ) {
			error_log( 'エラー発生：' . $e->getMessage() );
			$err_msg['common'] = MSG_07;
		}
	}
}
debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );

$title = '退会';
require HEAD;
require HEADER;
?>
    <main id="main" class="l-main">
        <section class="c-container">
            <h2 class="c-subTitle c-subTitle--col2">マイページ</h2>
            <div class="c-container__col2">
				<?php require SIDEBAR; ?>
                <div class="c-container__main">
                    <div class="c-container__body">
                        <h3 class="c-thrTitle c-thrTitle--center">退会</h3>
                        <form action="" method="post" class="c-authForm">
                            <p class="u-fontSize-s u-marginB-l">
                                退会は即時反映され、一度退会すると復帰できません。
                            </p>
                            <div class="c-areaMsg c-areaMsg--common">
								<?php echo getErrMsg( 'common' ); ?>
                            </div>
                            <label for="reason1">
                                退会理由
                                <span class="c-areaMsg"><?php echo getErrMsg( 'reason1' ); ?></span>
                            </label>
                            <div class="c-authForm__select c-triangle c-triangle--right <?php echo getErrClass( 'reason1' ); ?>">
                                <select name="reason1" id="reason1">
                                    <option value>選択してください</option>
									<?php
									foreach ( $withdrawalCategory as $option ) {
										if ( ! empty( $_POST['reason1'] ) && $_POST['reason1'] === $option['id'] ) {
											echo '<option value="' . $option['id'] . '" selected>' . $option['reason'] . '</option>';
										} else {
											echo '<option value="' . $option['id'] . '">' . $option['reason'] . '</option>';
										}
									}
									?>
                                </select>
                            </div>
                            <label class="<?php echo getErrClass( 'reason2' ); ?>" for="reason2">
                                詳しい理由
                                <span>　※任意</span>
                                <span class="c-areaMsg"><?php echo getErrMsg( 'reason2' ); ?></span>
                            </label>
                            <textarea name="reason2" id="reason2" cols="30" rows="10"
                                      class="c-authForm__textarea js-counterText"><?php echo getMethodHold( 'reason2' ); ?></textarea>
                            <p class="c-counter"><span
                                        class="js-showCounter"><?php echo mb_strlen( getMethodHold( 'reason2' ) ); ?></span>/500
                            </p>
                            <input type="submit" class="c-btn c-btn--center" value="退会する">
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?php
require FOOTER;
?>