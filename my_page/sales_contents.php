<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『『　作品管理ページ　『『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
authLogin();
debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );
$title = 'マイページ';
require HEAD;
require HEADER;
?>
    <main id="main" class="l-main">
        <section class="c-container">
            <h2 class="c-subTitle c-subTitle--col2">マイページ</h2>
            <div class="c-container__col2">
				<?php require SIDEBAR; ?>
                <div class="c-container__main">
                    <div class="c-container__head">
                        <div id="js-headLeftTarget" class="c-container__headLeft"></div>
                        <div id="js-headRightTarget" class="c-container__headRight c-dropDown"></div>
                    </div>
                    <div id="js-wrapTarget" class="c-container__body"></div>
                </div>
            </div>
        </section>
    </main>
<?php
require FOOTER;
?>