<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『『『　掲示板ページ　『『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
authLogin();

$_SESSION['pageOpenTime'] = date( 'Y-m-d H:i:s' );
// 画面表示用データ取得
//================================
// GETパラメータを取得
$p_id = ( ! empty( $_GET['p_id'] ) ) ? $_GET['p_id'] : '';

$viewData = getMsgs( $p_id );
debug( '$viewData: ' . print_r( $viewData, true ) );

// 商品情報を取得
$productInfo = getProductDetail( $p_id, true );
// 商品情報が入っているかチェック
if ( empty( $productInfo ) ) {
	error_log( 'エラー発生:商品情報が取得できませんでした' );

	return false;
}
//DBから投稿ユーザー情報を取得
$userInfo = getUser( $productInfo['user_id'] );
//投稿ユーザー情報が取れたかチェック
if ( empty( $userInfo ) ) {
	error_log( 'エラー発生:ユーザー情報が取得できませんでした' );

	return false;
}

debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );
$title = '掲示板';
require HEAD;
require HEADER;
?>
    <main id="main" class="l-main">
        <section class="c-container">
            <h2 class="c-subTitle c-subTitle--col2">マイページ</h2>
            <div class="c-container__col2">
				<?php require SIDEBAR; ?>
                <div class="c-container__main">
                    <div class="c-container__body p-msg">
                        <h3 class="c-thrTitle c-thrTitle--center">掲示板</h3>
                        <div class="p-msg__head c-contents">
                            <div class="p-msg__img c-contents__img">
                                <img src="<?php echo sanitize( showImg( $productInfo['pic1'] ) ); ?>"
                                     alt="<?php echo sanitize( $productInfo['name'] ); ?>">
                            </div>
                            <div class="p-msg__info">
                                <div class="p-msg__infoItem">
                                    <span class="p-msg__infoLabel">作品名</span>
                                    <p><?php echo sanitize( $productInfo['name'] ); ?></p>
                                </div>
                                <div class="p-msg__infoItem">
                                    <p><span class="p-msg__infoLabel">作成者</span></p>
                                    <a class="c-flex c-flex--alignCen"
                                       href="<?php echo PUB_USER_PAGE . '?sh=' . hash( "sha256", sanitize( $userInfo['id'] . $userInfo['create_date'] ) ); ?>">
                                        <div class="c-user">
                                            <div class="c-user__icon">
                                                <img src="<?php echo sanitize( showImg( $userInfo['pic'] ) ); ?>"
                                                     alt="<?php echo isset( $userInfo['nick_name'] ) ? sanitize( $userInfo['nick_name'] ) : 'no name'; ?>">
                                            </div>
                                        </div>
                                        <p class="p-msg__userName"><?php echo isset( $userInfo['nick_name'] ) ? sanitize( $userInfo['nick_name'] ) : 'no name'; ?></p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="p-msg__body">
                            <div class="p-msg__msgArea js-scrollBottom js-appendMsg">
								<?php
								if ( ! empty( $viewData ) && ! empty( $viewData[0]['msg'] ) ) {
									foreach ( $viewData as $val ) {
										if ( ! empty( $val['from_user_id'] ) && $val['from_user_id'] != $_SESSION['user_id'] ) {
											?>
                                            <div class="p-msg__left">
                                                <div class="c-user p-msg__userIcon">
                                                    <div class="c-user__icon c-user__icon--msg">
                                                        <img src="<?php echo sanitize( showImg( $val['pic'] ) ); ?>"
                                                             alt="<?php echo isset( $val['nick_name'] ) ? sanitize( $val['nick_name'] ) : 'no name'; ?>">
                                                    </div>
                                                </div>
                                                <div class="p-msg__speech">
                                                    <span class="p-msg__triangle"></span>
                                                    <p class="p-msg__text">
														<?php echo sanitize( $val['msg'] ); ?>
                                                    </p>
                                                    <p class="p-msg__date"><?php echo sanitize( $val['send_date'] ); ?></p>
                                                </div>
                                            </div>
											<?php
										} else {
											?>
                                            <div class="p-msg__right ">
                                                <div class="c-user p-msg__userIcon">
                                                    <div class="c-user__icon c-user__icon--msg">
                                                        <img src="<?php echo sanitize( showImg( $val['pic'] ) ); ?>"
                                                             alt="<?php echo isset( $val['nick_name'] ) ? sanitize( $val['nick_name'] ) : 'no name'; ?>">
                                                    </div>
                                                </div>
                                                <div class="p-msg__speech">
                                                    <span class="p-msg__triangle"></span>
                                                    <p class="p-msg__text">
														<?php echo sanitize( $val['msg'] ); ?>
                                                    </p>
                                                    <p class="p-msg__date"><?php echo sanitize( $val['send_date'] ); ?></p>
                                                </div>
                                            </div>
											<?php
										}
									}
								} else {
									?>
                                    <p class="p-msg__none js-hide">メッセージ投稿はまだありません</p>
									<?php
								}
								?>
                                <template id="msgLeft">
                                    <div class="p-msg__left">
                                        <div class="c-user p-msg__userIcon">
                                            <div class="c-user__icon c-user__icon--msg">
                                                <img src="" alt="" class="js-insertImg">
                                            </div>
                                        </div>
                                        <div class="p-msg__speech">
                                            <span class="p-msg__triangle"></span>
                                            <p class="p-msg__text js-insertMsg"></p>
                                            <p class="p-msg__date js-insertDate"></p>
                                        </div>
                                    </div>
                                </template>
                                <template id="msgRight">
                                    <div class="p-msg__right ">
                                        <div class="c-user p-msg__userIcon">
                                            <div class="c-user__icon c-user__icon--msg">
                                                <img src="" alt="" class="js-insertImg">
                                            </div>
                                        </div>
                                        <div class="p-msg__speech">
                                            <span class="p-msg__triangle"></span>
                                            <p class="p-msg__text js-insertMsg"></p>
                                            <p class="p-msg__date js-insertDate"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="p-msg__sendArea">
                                <form action="" method="post" class="p-msg__sendForm js-submitForm">
                                    <textarea name="send_msg" id="" cols="30" rows="10" class="p-msg__input"></textarea>
                                    <input type="submit" value="送信する" class="c-btn c-btn--right">
                                </form>
                            </div>
                        </div>
                        <div class="u-fontSize-s">
                            *相手のことを考え丁寧なコメントを心掛けましょう。<br>
                            *不快な発言、言葉遣いを繰り返す場合、退会となることがあります。
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?php
require FOOTER;
?>