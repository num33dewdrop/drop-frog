<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『『　作品詳細ページ　『『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

//画面表示用データ取得
//================================
//GETデータを格納
$p_id = ! empty( $_GET['p_id'] ) ? $_GET['p_id'] : '';
//DBから作品データ取得
$viewData       = getProductDetail( $p_id );
$dbCategoryData = getProductCategory();
//パラメータの不正入力チェック
if ( empty( $viewData ) || empty( $dbCategoryData ) ) {
	error_log( 'エラー発生:指定ページに不正な値が入りました' );
	header( "Location:" . HOME );
	exit();
}
debug( '$viewData:' . print_r( $viewData, true ) );
debug( '$viewData:' . print_r( $viewData['category'], true ) );
//関連作品データ取得
$viewRelationData = getProductRelation( $viewData['category_id'] );

foreach ( $dbCategoryData as $key => $val ) {
	if ( $viewData['category_id'] === $val['id'] ) {
		$pic1 = empty( $viewData['pic1'] ) ? '../src/img/p_category' . $val['id'] . '.jpg' : $viewData['pic1'];
	}
}

//閲覧履歴処理
//ログイン済みかつ、自分の作品以外かつ、まだ登録されていない場合
if ( ! empty( $_SESSION['user_id'] ) && $_SESSION['user_id'] !== $viewData['user_id'] ) {
	$viewCount = getCountBrowsing( $_SESSION['user_id'], $p_id );
	try {
		// DBへ接続
		$dbh  = dbConnect();
		$data = array( ':u_id' => $_SESSION['user_id'], ':p_id' => $p_id );
		if ( ! $viewCount ) {
			// SQL文作成
			$sql  = 'INSERT INTO browsing_history(product_id, customer_user_id, view_count, create_date) VALUES (:p_id, :u_id, :view_count, :create_date)';
			$data = array_merge( $data, array( ':view_count' => 1, ':create_date' => date( 'Y-m-d H:i:s' ) ) );
		} else {
			$sql  = 'UPDATE browsing_history SET view_count = :view_count WHERE product_id = :p_id AND customer_user_id = :u_id';
			$data = array_merge( $data, array( ':view_count' => $viewCount['view_count'] + 1 ) );
		}
		// クエリ実行
		$stmt = queryPost( $dbh, $sql, $data );
		dbAffect( $stmt );
	} catch ( Exception $e ) {
		error_log( 'エラー発生:' . $e->getMessage() );
	}
}

if ( ! empty( $_POST ) ) {
	authLogin();
	debug( '連絡掲示板へ遷移します。' );
	header( "Location:" . MSG . '?p_id=' . $p_id );
	exit();
}

debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );
$title = '作品詳細';
require HEAD;
require HEADER;
?>
    <main id="main" class="l-main">
        <section class="c-container p-product <?php if ( empty( $viewData['pic2'] ) ) {
			echo 'p-product--noImg';
		} ?>">
            <h2 class="c-subTitle c-subTitle--col2">作品詳細</h2>
            <div class="c-container__col2">
                <div class="p-product__imgContainer c-flex c-contents">
                    <div class="p-product__imgContainerMain <?php if ( empty( $viewData['pic2'] ) ) {
						echo 'p-product__imgContainerMain--noImg';
					} ?>">
                        <div class="p-product__img p-product__img--main c-contents__img">
                            <img src="<?php echo sanitize( showImg( $pic1 ) ); ?>"
                                 alt="<?php echo sanitize( $viewData['name'] ); ?>" class="js-switch-img-main">
                        </div>
                    </div>
					<?php if ( ! empty( $viewData['pic2'] ) ) { ?>
                        <div class="p-product__imgContainerSubs">
							<?php for ( $i = 2; $i <= 5; $i ++ ) { ?>
                                <button class="p-product__img p-product__img--sub c-contents__img">
                                    <img src="<?php echo sanitize( showImg( $viewData[ 'pic' . $i ] ) ); ?>"
                                         alt="<?php echo sanitize( $viewData['name'] ); ?>"
                                         class="<?php if ( ! empty( $viewData[ 'pic' . $i ] ) ) {
										     echo 'js-switch-img-sub';
									     } ?>">
                                </button>
							<?php } ?>
                        </div>
					<?php } ?>
                </div>
                <div class="p-product__detail">
                    <div class="c-flex c-flex--spaceBet p-product__info">
                        <div>
                            <span class="p-product__label">作品名</span>
                            <p><?php echo sanitize( $viewData['name'] ); ?></p>
                        </div>
                        <button class="p-product__favorite js-favorite c-flex c-flex--alignCen c-flex--justCenter c-flex--column <?php if ( ! empty( $_SESSION['user_id'] ) && isLike( $_SESSION['user_id'], $viewData['id'] ) ) {
							echo 'js-favorite--active';
						} ?>" data-pid="<?php echo sanitize( $viewData['id'] ); ?>">
                            <i class="fa-heart u-fontSize-m <?php echo ( ! empty( $_SESSION['user_id'] ) && isLike( $_SESSION['user_id'], $viewData['id'] ) ) ? 'fa-solid' : 'fa-regular'; ?>"
                               aria-hidden="true"></i>
                            <span class="js-favoriteCount"><?php echo sanitize( getFavoriteNum( $viewData['id'] ) ); ?></span>
                        </button>
                    </div>
                    <div class="p-product__info">
                        <span class="p-product__label">作成者</span>
                        <p>
                            <a href="<?php echo PUB_USER_PAGE . '?sh=' . hash( "sha256", sanitize( $viewData['user_id'] . $viewData['create_date'] ) ); ?>">
								<?php echo ! empty( $viewData['nick_name'] ) ? sanitize( $viewData['nick_name'] ) : '名無し'; ?>
                            </a>
                        </p>
                    </div>
                    <div class="p-product__info">
                        <span class="p-product__label">カテゴリ</span>
                        <p>
                            <a href="<?php echo PRODUCT_LIST . '?c_id=' . sanitize( $viewData['category_id'] ); ?>">
								<?php echo sanitize( $viewData['category'] ); ?>
                            </a>
                        </p>
                    </div>
                    <div class="p-product__info">
                        <span class="p-product__label">URL</span>
                        <p>
							<?php if ( ! empty( $viewData['url'] ) ) { ?>
                                <a href="<?php echo sanitize( $viewData['url'] ); ?>" target="_blank">
									<?php echo sanitize( $viewData['url'] ); ?>
                                </a>
							<?php } else { ?>
                                URLの登録はありません
							<?php } ?>
                        </p>
                    </div>
                    <div class="p-product__info">
                        <span class="p-product__label">GitHub</span>
                        <p>
			                <?php if ( ! empty( $viewData['repository'] ) ) { ?>
                                <a href="https://github.com/<?php echo sanitize( $viewData['git_name'] ).'/'.sanitize( $viewData['repository'] ).'/'; ?>" target="_blank">
					                <?php echo sanitize( $viewData['repository'] ); ?>
                                </a>
			                <?php } else { ?>
                                GitHubの登録はありません
			                <?php } ?>
                        </p>
                    </div>
                    <form action="" method="post" class="u-marginB-l">
                        <input class="p-product__btn c-btn" type="submit" name="msg" value="コメントする">
                    </form>
                </div>
            </div>
			<?php if ( ! empty( $viewData['detail'] ) ) { ?>
                <div class="p-product__info">
                    <span class="p-product__label">作品説明</span>
                    <p class="u-pre"><?php echo sanitize( $viewData['detail'] ); ?></p>
                </div>
			<?php } ?>
        </section>
		<?php if ( ! empty( $viewRelationData ) ) { ?>
            <section class="c-container">
                <h2 class="c-subTitle">関連作品</h2>
                <div class="c-contents">
                    <ul class="c-contents__list c-contents__list--col5 c-contents__list--spScroll">
						<?php foreach ( $viewRelationData as $val ) { ?>
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
                        <a href="<?php echo PRODUCT_LIST . '?c_id=' . sanitize( $viewData['category_id'] ); ?>"
                           class="c-line__btn">もっと見る</a>
                    </div>
                </div>
            </section>
		<?php } ?>
    </main>
<?php
require FOOTER;
?>