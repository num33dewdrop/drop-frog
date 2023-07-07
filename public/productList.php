<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『『　作品一覧ページ　『『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

//画面表示用データ取得
//================================
//カレントページ
$currentPageNum = ! empty( $_GET['p'] ) ? $_GET['p'] : 1; //デフォルトは１ページめ
//カテゴリー
$category = ! empty( $_GET['c_id'] ) ? $_GET['c_id'] : '';
//ソート順
$sort = ! empty( $_GET['sort'] ) ? $_GET['sort'] : '';
//キーワード
$q = ! empty( $_GET['q'] ) ? $_GET['q'] : '';

//パラメータの不正入力チェック
if ( ! is_int( (int) $currentPageNum ) ) {
	error_log( 'エラー発生:指定ページに不正な値が入りました' );
	header( "Location:" . HOME );
	exit();
}
//作品表示件数
$listSpan = 12;
//現在の表示レコード先頭を算出
$currentMinNum = ( $currentPageNum - 1 ) * $listSpan; //1ページ目なら(1-1)*15 = 0 、 ２ページ目なら(2-1)*15 = 15
//DBから作品データを取得
$dbProductData = getProductList( $category, $sort, $currentMinNum, $q );
//DBからカテゴリデータを取得
$dbCategoryData = getProductCategory();


debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );
$title = '作品一覧';
require HEAD;
require HEADER;
?>
    <main id="main" class="l-main">
        <section class="c-container">
            <h2 class="c-subTitle c-subTitle--col2">
				<?php
				if ( getMethodHold( 'q', false, true ) ) {
					echo '検索ワード【' . getMethodHold( 'q', false, true ) . '】';
				}
				if ( getMethodHold( 'q', false, true ) && getMethodHold( 'c_id', false, true ) ) {
					echo ' | ';
				}
				if ( ! getMethodHold( 'q', false, true ) && ! getMethodHold( 'c_id', false, true ) ) {
					echo '作品';
				}
				if ( ! empty( $dbCategoryData ) ) {
					foreach ( $dbCategoryData as $key => $val ) {
						if ( (int) getMethodHold( 'c_id', false, true ) === ( $key + 1 ) ) {
							echo sanitize( $val['name'] );
						}
					}
				}
				?>一覧
            </h2>
            <div class="c-container__col2">
                <aside class="c-container__sidebar c-spSlideMenu js-toggleSpSidebarTarget">
                    <div class="c-spHideBtn js-spSidebarHide">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                    <h3 class="c-container__sideTitle">カテゴリ</h3>
                    <ul class="c-sidebarList">
                        <li class="c-sidebarList__item">
                            <button class="c-sidebarList__link <?php if ( getMethodHold( 'c_id', false, true ) == 0 ) {
								echo 'c-sidebarList__link--active';
							} ?> js-insertCategorySelect" type="submit" name="c_id" value="0">すべてのカテゴリー
                            </button>
                        </li>
						<?php
						if ( ! empty( $dbCategoryData ) ) {
							foreach ( $dbCategoryData as $list ) {
								?>
                                <li class="c-sidebarList__item">
                                    <button class="c-sidebarList__link <?php if ( getMethodHold( 'c_id', false, true ) == $list['id'] ) {
										echo 'c-sidebarList__link--active';
									} ?> js-insertCategorySelect" type="submit" name="c_id"
                                            value="<?php echo $list['id']; ?>">
										<?php echo $list['name'] ?>
                                    </button>
                                </li>
								<?php
							}
						}
						?>
                    </ul>
                </aside>
                <button class="c-btn c-btn--right c-btn--sp js-toggleSpSidebar">絞り込み検索</button>
                <div class="c-container__main">
                    <div class="c-container__head">
                        <div class="c-container__headLeft">
                            <h3 class="c-thrTitle">検索結果</h3>
                            <p class="c-contentsNum">
								<?php echo ! empty( $dbProductData['data'] ) ? $currentMinNum + 1 : 0; ?> -
								<?php echo ! empty( $dbProductData['data'] ) ? $currentMinNum + count( $dbProductData['data'] ) : 0; ?>
                                件 表示　/
								<?php echo ! empty( $dbProductData['data'] ) ? sanitize( $dbProductData['total'] ) : 0; ?>
                                件中
                            </p>
                        </div>
                        <div class="c-container__headRight c-dropDown">
                            <button class="c-triangle c-triangle--right">
                            <span class="c-dropDown__current js-dropDown__current">
                                <?php foreach ( $sortPData as $key => $val ) {
	                                if ( (int) getMethodHold( 'sort', false, true ) === $key ) {
		                                echo sanitize( $val );
	                                }
                                } ?>
                            </span>
                            </button>
                            <ul class="c-dropDown__list js-dropDown__target">
								<?php foreach ( $sortPData as $key => $val ) { ?>
                                    <li class="c-dropDown__item js-dropDown__item">
                                        <button class="c-dropDown__btn js-appendGet" name="sort"
                                                value="<?php echo $key; ?>">
											<?php echo sanitize( $val ); ?>
                                        </button>
                                    </li>
								<?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="c-container__body">
						<?php if ( ! empty( $dbProductData['data'] ) ) { ?>
                            <div class="c-contents">
                                <ul class="c-contents__list c-contents__list--wrap c-contents__list--col3">
									<?php foreach ( $dbProductData['data'] as $val ) { ?>
                                        <li class="c-contents__item c-contents__item--col3">
                                            <a href="<?php echo PRODUCT_DETAIL . ( ! empty( appendLinkParam() ) ? appendLinkParam() . '&p_id=' . sanitize( $val['id'] ) : '?p_id=' . sanitize( $val['id'] ) ); ?>"
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
							<?php pagination( $currentPageNum, $dbProductData['total_page'], appendPageParam( array( 'p' ) ) ); ?>
						<?php } else { ?>
                            <div class="c-contents c-contents--none">
                                該当の作品はありません。
                            </div>
						<?php } ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?php
require FOOTER;
?>