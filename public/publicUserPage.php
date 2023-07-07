<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『『　ユーザーページ　『『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();


// 画面表示用データ取得
//================================
$sh = ! empty( $_GET['sh'] ) ? $_GET['sh'] : '';
//ユーザー情報
$userInfo = getPubUserInfo( $sh );
//パラメータの不正入力チェック
if ( empty( $userInfo ) ) {
	error_log( 'エラー発生:指定ページに不正な値が入りました' );
	header( "Location:" . HOME );
	exit();
}
//出品作品取得
$dbProductData = getMyProducts( $userInfo['id'], 0, 0, 12, true );
$twitter       = ! empty( $userInfo['twitter'] ) ? str_replace( "@", "", $userInfo['twitter'] ) : null;
debug( print_r( $dbProductData, true ) );
debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );
$title = 'ユーザーページ';
require HEAD;
require HEADER;
?>
    <main id="main" class="l-main">
        <section class="c-container">
            <h2 class="c-subTitle"><?php echo isset( $userInfo['nick_name'] ) ? sanitize( $userInfo['nick_name'] ) : 'no name。'; ?></h2>
            <div class="p-publicUser">
                <div class="c-user">
                    <div class="c-user__head">
                        <div class="c-user__icon">
                            <img src="<?php echo sanitize( showImg( $userInfo['pic'] ) ); ?>"
                                 alt="<?php echo isset( $userInfo['nick_name'] ) ? sanitize( $userInfo['nick_name'] ) : 'no name。'; ?>">
                        </div>
                    </div>
                    <div class="c-user__body">
                        <div class="c-user__info">
							<?php echo ! empty( $dbProductData['total'] ) ? sanitize( $dbProductData['total'] ) : 0; ?>
                            作品 掲載中
                        </div>
                        <div class="c-flex c-flex--spaceAro">
							<?php if ( ! empty( $twitter ) ) { ?>
                                <div class="c-user__info">
                                    <p class="c-user__label">Twitter</p>
                                    <a href="https://twitter.com/<?php echo sanitize( $twitter ); ?>" target="_blank">
                                        <i class="fa-brands fa-square-twitter"></i>
                                    </a>
                                </div>
							<?php } ?>
							<?php if ( ! empty( $userInfo['git_hub'] ) ) { ?>
                                <div class="c-user__info">
                                    <p class="c-user__label">GitHub</p>
                                    <a href="https://github.com/<?php echo sanitize( $userInfo['git_hub'] ); ?>/"
                                       target="_blank">
                                        <i class="fa-brands fa-github"></i>
                                    </a>
                                </div>
							<?php } ?>
							<?php if ( ! empty( $userInfo['blog'] ) ) { ?>
                                <div class="c-user__info">
                                    <p class="c-user__label">Blog</p>
                                    <a href="<?php echo sanitize( $userInfo['blog'] ); ?>" target="_blank">
                                        <i class="fa-sharp fa-solid fa-blog"></i>
                                    </a>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                </div>
				<?php if ( ! empty( $userInfo['introduction'] ) ) { ?>
                    <div class="p-publicUser__detail js-showText c-triangle c-triangle--bottom">
                        <p class="p-publicUser__text js-showText__target u-pre">
							<?php echo sanitize( $userInfo['introduction'] ); ?>
                        </p>
                    </div>
				<?php } else { ?>
                    <div class="p-publicUser__detail">
                        <p class="p-publicUser__text p-publicUser__text--none">
                            自己紹介文はありません。
                        </p>
                    </div>
				<?php } ?>
            </div>
        </section>
        <section class="c-container js-showFullContainer">
            <h2 class="c-subTitle js-subTitlePos">投稿作品</h2>
			<?php if ( ! empty( $dbProductData['data'] ) ) { ?>
                <div class="c-contents">
                    <ul class="c-contents__list c-contents__list--wrap c-contents__list--col5 c-contents__list--center">
						<?php foreach ( $dbProductData['data'] as $val ) { ?>
                            <li class="c-contents__item c-contents__item--col5 js-showFullTarget">
                                <a href="<?php echo PRODUCT_DETAIL . '?p_id=' . sanitize( $val['p_id'] ); ?>"
                                   class="c-contents__itemContainer">
                                    <div class="c-contents__img">
                                        <img src="<?php echo sanitize( showImg( $val['pic1'] ) ); ?>"
                                             alt="<?php echo sanitize( $val['name'] ); ?>">
                                    </div>
                                    <div class="c-contents__body">
                                        <p class="c-contents__name"><?php echo sanitize( $val['name'] ); ?></p>
                                    </div>
                                </a>
                            </li>
						<?php } ?>
                    </ul>
                    <div class="c-line">
                        <button class="c-line__btn js-showFullBtn">もっと見る</button>
                    </div>
                </div>
			<?php } else { ?>
                <div class="c-contents c-contents--none">
                    該当の作品はありません。
                </div>
			<?php } ?>
        </section>
    </main>
<?php
require FOOTER;
?>