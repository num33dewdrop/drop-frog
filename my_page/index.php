<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『　ダッシュボードページ　『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
authLogin();

// 画面表示用データ取得
//================================
//ユーザー情報
$userInfo = getUser( $_SESSION['user_id'] );
//お知らせ用
$news = getProductNews( $_SESSION['user_id'] );
//掲示板用
$myMsg = getMyMsgsAndBoard( $_SESSION['user_id'] );

debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );
$title = 'ダッシュボード';
require HEAD;
require HEADER;
?>
    <main id="main" class="l-main">
        <section class="c-container">
            <h2 class="c-subTitle c-subTitle--col2">マイページ</h2>
            <div class="c-container__col2">
				<?php require SIDEBAR; ?>
                <div class="c-container__main">
                    <h3 class="c-thrTitle c-thrTitle--center"><?php echo isset( $userInfo['nick_name'] ) ? sanitize( $userInfo['nick_name'] ) : 'no name'; ?></h3>
                    <div class="c-user">
                        <a href="<?php echo PUB_USER_PAGE . '?sh=' . hash( "sha256", sanitize( $userInfo['id'] . $userInfo['create_date'] ) ); ?>"
                           class="c-user__head">
                            <div class="c-user__icon">
                                <img src="<?php echo sanitize( showImg( $userInfo['pic'] ) ); ?>"
                                     alt="<?php echo isset( $userInfo['nick_name'] ) ? sanitize( $userInfo['nick_name'] ) : 'no name'; ?>">
                            </div>
                        </a>
                    </div>
                    <div class="c-container__body">
                        <h3 class="c-thrTitle c-thrTitle--center">お知らせ</h3>
                        <div class="c-news c-news--scroll u-marginB-xl">
							<?php if ( ! empty( $news ) ) { ?>
                                <form action="" method="post">
                                    <ul class="c-news__list">
										<?php foreach ( $news as $key => $val ) {
											if ( $val['notice_type'] === 'favorite' ) { ?>
                                                <li class="c-news__item">
                                                    <p class="c-news__head">
                                                        <span class="c-news__headDay"><?php echo sanitize( $val['create_date'] ); ?></span>
                                                        【<span class="c-news__name"><?php echo sanitize( $val['name'] ); ?></span>】
                                                    </p>
                                                    <div class="c-news__body">
                                                        <a href="<?php echo PUB_USER_PAGE . '?sh=' . hash( "sha256", sanitize( $val['customer_user_id'] . $val['hash'] ) ); ?>"
                                                           class="c-news__name">
															<?php echo isset( $val['nick_name'] ) ? sanitize( $val['nick_name'] ) : 'no name'; ?>
                                                        </a>
                                                        <span>さんがいいね！しました。</span>
                                                    </div>
                                                </li>
											<?php }
										} ?>
                                    </ul>
                                </form>
							<?php } else { ?>
                                お知らせはまだありません。
							<?php } ?>
                        </div>
                        <h3 class="c-thrTitle c-thrTitle--center">掲示板</h3>
                        <div class="c-news c-news--scroll">
							<?php if ( ! empty( $myMsg ) ) { ?>
                                <ul class="c-news__list">
									<?php foreach ( $myMsg as $key => $val ) { ?>
                                        <li class="c-news__item">
                                            <p class="c-news__head">
                                                <span class="c-news__headDay"><?php echo sanitize( $val['send_date'] ); ?></span>
                                                【<span class="c-news__name"><?php echo sanitize( $val['name'] ); ?></span>】
                                            </p>
                                            <p class="c-news__body">
                                                <a href="<?php echo PUB_USER_PAGE . '?sh=' . hash( "sha256", sanitize( $val['customer_user_id'] . $val['hash'] ) ); ?>"
                                                   class="c-news__name">
													<?php echo isset( $val['nick_name'] ) ? sanitize( $val['nick_name'] ) : 'no name'; ?>
                                                </a>：
                                                <span class="c-news__msg"><?php echo sanitize( $val['msg'] ); ?></span>
                                            </p>
                                            <div class="c-line">
                                                <a href="<?php echo MSG . '?p_id=' . sanitize( $val['p_id'] ); ?>"
                                                   class="c-line__btn c-line__btn--msg">
                                                    コメントする
                                                </a>
                                            </div>
                                        </li>
									<?php } ?>
                                </ul>
							<?php } else { ?>
                                メッセージはまだありません。
							<?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?php
require FOOTER;
?>