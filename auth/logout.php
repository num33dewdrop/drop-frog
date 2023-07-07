<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『『　ログアウトページ　『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
debug( 'ログアウトします' );
//セッションを削除（ログアウトする）
session_destroy();
debug( 'TOPページへ遷移します' );
header( 'Location:' . HOME );
exit();