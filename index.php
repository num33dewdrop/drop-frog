<?php
//共通変数・関数ファイルを読込み
require( './configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『『　トップページ　『『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

header( 'Location:' . HOME );
debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );