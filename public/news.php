<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『『　ニュースページ　『『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();


//画面表示用データ取得
//================================
$n_id = ! empty( $_GET['n_id'] ) ? $_GET['n_id'] : '';
if ( ! is_int( (int) $n_id ) ) {
	error_log( 'エラー発生:指定ページに不正な値が入りました' );
	header( "Location:" . HOME );
	exit();
}
$dbNewsData = getNewsOne( $n_id );
debug( print_r( $dbNewsData, true ) );

debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );
$title = 'お知らせ';
require HEAD;
require HEADER;
?>
<main id="main" class="l-main">
    <section class="c-container">
        <div class="c-fixedPage">
            <h2 class="c-fixedPage__title c-fixedPage__title--main">お知らせ</h2>
            <h3 class="c-thrTitle">
				<?php echo sanitize( $dbNewsData['title'] ); ?>
            </h3>
            <p class="c-fixedPage__text u-pre">
				<?php echo sanitize( $dbNewsData['detail'] ); ?>
            </p>
            <p class="c-fixedPage__text">
                <span><?php echo sanitize( $dbNewsData['update_date'] ); ?></span>
            </p>
        </div>
    </section>
</main>
<?php
require FOOTER;
?>
