<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『『『　トップページ　『『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

// 画面表示用データ取得
//================================
//DBから作品データを取得
$dbFavProductData = getProductListAndFavNum();
$dbNewProductData = getProductList( null, 0, 0, null, 5 );
$dbCategoryData   = getProductCategory();
$dbNewsData       = getNews();
debug('カテゴリー：'.print_r($dbCategoryData, true));
debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );
require HEAD;
require HEADER;
?>
    <main id="main" class="l-main">
        <section class="c-slider c-container">
			<?php if ( ! empty( $dbFavProductData ) ) { ?>
                <div class="c-slider__wrap c-contents">
                    <i class="fa-solid c-slider__nav c-slider__nav--prev js-slider--prev fa-angle-left"></i>
                    <i class="fa-solid c-slider__nav c-slider__nav--next js-slider--next fa-angle-right"></i>
                    <div class="c-slider__container">
                        <ul class="c-contents__list c-slider__list js-sliderList">
							<?php foreach ( $dbFavProductData as $val ) { ?>
                                <li class="c-contents__item c-contents__item--col3">
                                    <a href="<?php echo PRODUCT_DETAIL . '?p_id=' . sanitize( $val['id'] ); ?>"
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
                    </div>
                </div>
			<?php } ?>
        </section>
        <section class="c-container">
            <h2 class="c-subTitle">新着</h2>
			<?php if ( ! empty( $dbNewProductData['data'] ) ) { ?>
                <div class="c-contents">
                    <ul class="c-contents__list c-contents__list--col5 c-contents__list--spScroll">
						<?php foreach ( $dbNewProductData['data'] as $val ) { ?>
                            <li class="c-contents__item c-contents__item--col5">
                                <a href="<?php echo PRODUCT_DETAIL . '?p_id=' . sanitize( $val['id'] ); ?>"
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
                        <a href="<?php echo PRODUCT_LIST; ?>" class="c-line__btn">もっと見る</a>
                    </div>
                </div>
			<?php } ?>
        </section>
        <section class="c-container js-showFullContainer">
            <h2 class="c-subTitle js-subTitlePos">人気カテゴリ</h2>
			<?php if ( ! empty( $dbCategoryData ) ) { ?>
                <div class="c-contents">
                    <ul class="c-contents__list c-contents__list--wrap c-contents__list--col5">
						<?php foreach ( $dbCategoryData as $val ) { ?>
                            <li class="c-contents__item c-contents__item--col5 js-showFullTarget">
                                <a href="<?php echo PRODUCT_LIST . '?c_id=' . sanitize( $val['id'] ); ?>"
                                   class="c-contents__itemContainer">
                                    <div class="c-contents__img">
                                        <img src="<?php echo sanitize( showImg( $val['img'] ) ); ?>"
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
			<?php } ?>
        </section>
        <section class="p-topInfo c-container">
            <section class="p-topInfo__news c-contents js-showFullContainer">
                <h2 class="c-subTitle js-subTitlePos">お知らせ</h2>
				<?php if ( ! empty( $dbNewsData ) ) { ?>
                    <div class="c-news">
                        <ul class="c-news__list">
							<?php foreach ( $dbNewsData as $val ) { ?>
                                <li class="c-news__item js-showFullTarget js-showFullTarget--num3">
                                    <p class="c-news__head">
                                        <span><?php echo sanitize( $val['update_date'] ); ?></span>
                                        <span><?php echo sanitize( $val['category_name'] ); ?></span>
                                    </p>
                                    <a href="<?php echo NEWS . '?n_id=' . sanitize( $val['n_id'] ) ?>">
                                        <p class="c-news__body"><?php echo sanitize( $val['title'] ) ?></p>
                                    </a>
                                </li>
							<?php } ?>
                        </ul>
                    </div>
                    <div class="c-line">
                        <button class="c-line__btn js-showFullBtn">もっと見る</button>
                    </div>
				<?php } ?>
            </section>
            <section class="p-topInfo__eidea">
                <h2 class="c-subTitle">DROP FROGとは</h2>
                <div class="p-eidea">
                    <div class="p-eidea__img">
                        <img src="../src/img/sebastian.jpg" alt="drop-frog">
                    </div>
                    <p class="p-eidea__detail">
                        『DROP FROG』は様々な駆け出しクリエイターが作品を展示し評価してもらえるコミュニティーサイトです<br>
                        <br>
                        <span class="u-fontWeight-B">自身の作品を公開し、評価し合うことで新たな気づきを見つけたい</span><br>
                        <br>
                        <span class="u-fontWeight-B">様々な分野の作品に触れてみたい</span><br>
                        <br>
                        <span class="u-fontWeight-B">趣味で作ったはいいが、公開する所がない</span><br>
                        <br>
                        そんな駆け出し達の憩いの場になればと思います
                    </p>
                </div>
            </section>
        </section>
    </main>
<?php
require FOOTER;
?>