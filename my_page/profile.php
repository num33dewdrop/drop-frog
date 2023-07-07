<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『　プロフィール編集ページ　『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
authLogin();
//DBからユーザーデータを取得
$dbFormData = getUser( $_SESSION['user_id'] );
debug( '$dbFormData：' . print_r( $dbFormData, true ) );
//POST送信されていた場合
if ( ! empty( $_POST ) ) {
	debug( 'POST送信があります。' );
	debug( 'POST情報：' . print_r( $_POST, true ) );
	debug( 'FILE情報：' . print_r( $_FILES, true ) );
	//変数にユーザー情報を代入。
	//未入力チェックが無いので未入力の時に空文字を入れないようにする。DBはの初期値がnullのものはnullのまま
	//DBにデータがあれば、HTML内の入力保持関数でデータが入力された状態でPOSTされてくる
	//画像をアップロードし、パスを格納
	$pic = ! empty( $_FILES['pic']['name'] ) ? uploadImg( $_FILES['pic'], 'pic' ) : null;
	//画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
	$pic = ( empty( $pic ) && ! empty( $dbFormData['pic'] ) ) ? $dbFormData['pic'] : $pic;
	//何も入力がない場合nullで登録 == DBは更新されない
	$nick_name    = getMethodHold( 'nick_name' );
	$introduction = getMethodHold( 'introduction' );
	$twitter      = getMethodHold( 'twitter' );
	$blog         = getMethodHold( 'blog' );
	$git_hub      = getMethodHold( 'git_hub' );

	//DBの情報と入力情報が異なる場合にバリデーションを行う
	//最大文字数
	diffDb( $nick_name, 'nick_name', Closure::fromCallable( 'validMaxLen' ) );
	diffDb( $introduction, 'introduction', Closure::fromCallable( 'validMaxLen' ), 500 );
	diffDb( $twitter, 'twitter', Closure::fromCallable( 'validMaxLen' ) );
	diffDb( $blog, 'blog', Closure::fromCallable( 'validMaxLen' ) );
	diffDb( $git_hub, 'git_hub', Closure::fromCallable( 'validMaxLen' ) );
	//形式
	diffDb( $twitter, 'twitter', Closure::fromCallable( 'validTwitter' ) );
	diffDb( $blog, 'blog', Closure::fromCallable( 'validUrl' ) );

	if ( empty( $err_msg ) ) {
		debug( 'バリデーションOK' );
		try {
			$dbh  = dbConnect();
			$sql  = 'UPDATE users SET nick_name = :nick_name, introduction = :introduction, pic = :pic, twitter = :twitter, blog = :blog, git_hub = :git_hub WHERE id = :u_id';
			$data = array(
				':nick_name'    => $nick_name,
				':introduction' => $introduction,
				':pic'          => $pic,
				':u_id'         => $dbFormData['id'],
				':twitter'      => $twitter,
				':blog'         => $blog,
				':git_hub'      => $git_hub
			);
			$stmt = queryPost( $dbh, $sql, $data );
			if ( dbAffect( $stmt ) ) {
				$_SESSION['msg_success'] = SUC_02;

				if ( empty( $git_hub ) && ! empty( $dbFormData['git_hub'] ) ) {
					$sql  = 'UPDATE product SET git_hub = null  WHERE user_id = :u_id';
					$data = array( ':u_id' => $dbFormData['id'] );
					$stmt = queryPost( $dbh, $sql, $data );
					if ( ! dbAffect( $stmt ) ) {
						exit();
					}
				}

				debug( 'マイページへ遷移します。' );
				header( 'Location:' . MY_PAGE );
				exit();
			}
		} catch ( PDOException $e ) {
			error_log( 'エラー発生：' . $e->getMessage() );
			$err_msg['common'] = MSG_07;
		}
	}
}

debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );

$title = 'プロフィール編集';
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
                        <h3 class="c-thrTitle c-thrTitle--center">プロフィール編集</h3>
                        <form action="" method="post" class="c-editForm js-profileForm" enctype="multipart/form-data">
                            <div class="c-areaMsg c-areaMsg--common">
								<?php echo getErrMsg( 'common' ); ?>
                            </div>
                            <div class="c-editForm__imgContainer">
                                <div class="c-editForm__imgItem">
                                    <label for="pic" class="js-areaDrop c-user">プロフィール画像
                                        <div class="c-editForm__dropzone c-user__icon">
                                            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                            <input type="file" id="pic" name="pic" class="js-inputFile">
                                            <img src="<?php echo getDataAndHold( 'pic' ); ?>" alt="areaDrop"
                                                 class="js-prevTarget"
                                                 style="<?php if ( empty( getDataAndHold( 'pic' ) ) ) {
												     echo 'display: none';
											     } ?>">
                                        </div>
                                        <span class="c-areaMsg"><?php echo getErrMsg( 'pic' ); ?></span>
                                    </label>
                                </div>
                            </div>
                            <p class="c-editForm__caution">※JPEG・PNG・GIF形式の画像を登録してください。</p>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="nick_name" class="<?php echo getErrClass( 'nick_name' ); ?> ">
                                    ニックネーム
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'nick_name' ); ?></span>
                                </label>
                                <input type="text" id="nick_name" class="c-editForm__input" name="nick_name"
                                       value="<?php echo getDataAndHold( 'nick_name' ); ?>">
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="introduction" class="<?php echo getErrClass( 'introduction' ); ?>">
                                    自己紹介文
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'introduction' ); ?></span>
                                </label>
                                <textarea name="introduction" id="introduction" cols="30" rows="10"
                                          class="c-editForm__textarea js-counterText"><?php echo getDataAndHold( 'introduction' ); ?></textarea>
                                <p class="c-counter">
                                    <span class="js-showCounter">
                                        <?php echo mb_strlen( getDataAndHold( 'introduction' ) ); ?>
                                    </span>/500
                                </p>
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="twitter" class="<?php echo getErrClass( 'twitter' ); ?> ">
                                    Twitterアカウント
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'twitter' ); ?></span>
                                </label>
                                <input type="text" id="twitter" class="c-editForm__input" name="twitter"
                                       value="<?php echo getDataAndHold( 'twitter' ); ?>" placeholder="@example">
                                <p class="c-editForm__caution c-editForm__caution--left">※『＠アカウント名』のみ入力して下さい</p>
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="git_hub" class="<?php echo getErrClass( 'git_hub' ); ?> ">
                                    GitHubアカウント名
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'git_hub' ); ?></span>
                                </label>
                                <input type="text" id="git_hub" class="c-editForm__input js-deleteInput" name="git_hub"
                                       value="<?php echo getDataAndHold( 'git_hub' ); ?>"
                                       placeholder="example-username">
                                <p class="c-editForm__caution c-editForm__caution--left">※『アカウント名』のみ入力して下さい</p>
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="blog" class="<?php echo getErrClass( 'blog' ); ?> ">
                                    Blog
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'blog' ); ?></span>
                                </label>
                                <input type="text" id="blog" class="c-editForm__input" name="blog"
                                       value="<?php echo getDataAndHold( 'blog' ); ?>"
                                       placeholder="https://example.com">
                                <p class="c-editForm__caution c-editForm__caution--left">※『URL』を入力して下さい</p>
                            </div>
                            <div class="c-flex c-flex--justCenter u-paddingT-l">
                                <input type="submit" value="登録する" class="c-btn js-submitBtn" name="register">
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