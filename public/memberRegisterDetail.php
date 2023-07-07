<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『　会員登録のご案内ページ　『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );
$title = '会員登録のご案内';
require HEAD;
require HEADER;
?>
<main id="main" class="l-main">
    <section class="c-container">
        <div class="c-fixedPage">
            <h2 class="c-fixedPage__title c-fixedPage__title--main">無料会員登録のご案内</h2>
            <h3 class="c-fixedPage__title c-fixedPage__title--sub">DROP FROGの主な機能</h3>
            <h4 class="c-fixedPage__title c-fixedPage__title--thr">作品投稿</h4>
            <div class="c-fixedPage__col2">
                <p class="c-fixedPage__text c-fixedPage__text--col2">
                    自身の作品を投稿し、紹介することができます。<br>
                    画像を5枚まで登録できます。説明文、苦労した事、コンセプトなどを記載しましょう。<br>
                    <br>
                    --掲載できる内容--<br>
                    ・イラスト ・写真 ・アプリ ・ゲーム ・ウェブサービス ・ロゴ、バナー、ウェブデザイン<br>
                    <br>
                    サンプルサイト・サービス・アプリ・ゲームなどを他の利用者に実際に触れてもらうため、URLを記載できるようにしています。<br>
                    GitHubアカウントも登録可能です。ソースコードも含めて評価してもらいましょう。<br>
                    <br>
                    --注意点--<br>
                    写真・イラスト以外の投稿はURLの外部リンクを登録し、閲覧してもらいます。<br>
                    また、写真・イラスト・デザインについては、当サイトの画像が正方形表示のため、作品のフォーマットとそぐわない場合があります。<br>
                    そのため、どのような作品でも外部リンクの掲載でPRしてもらうことができます。<br>
                    <br>
                    まだ公開したくない場合は、公開設定により、下書き状態に留めておくことが可能です。<br>
                    公開設定はいつでも変更可能です。<br>
                </p>
                <div class="c-fixedPage__img c-fixedPage__img--col2">
                    <img src="../src/img/registerproduct.png" alt="register-img">
                </div>
            </div>
            <h4 class="c-fixedPage__title c-fixedPage__title--thr">検索機能</h4>
            <div class="c-fixedPage__col2">
                <p class="c-fixedPage__text c-fixedPage__text--col2">
                    フリーマーケットサイトのように全コンテンツから検索することができます。<br>
                    <br>
                    カテゴリ検索、ソート、キーワード検索が可能です。<br>
                    お気に入りのコンテンツを見つけましょう！<br>
                    <br>
                    また、自分の得意とする分野だけでなく、様々な分野の作品があります。<br>
                    様々な作品に触れ、自分の糧としましょう！
                </p>
                <div class="c-fixedPage__img c-fixedPage__img--col2">
                    <img src="../src/img/search.png" alt="search-img">
                </div>
            </div>
            <h4 class="c-fixedPage__title c-fixedPage__title--thr">コメント機能</h4>
            <div class="c-fixedPage__col2">
                <p class="c-fixedPage__text c-fixedPage__text--col2">
                    会員様には、各コンテンツに付随する掲示板にてコメントの閲覧、投稿が可能です。<br>
                    <br>
                    チャットのようなリアルタイムでのコメント投稿の閲覧が可能です。<br>
                    <br>
                    コンテンツに対するアドバイスや、質問等様々な会話を通して、クリエイター同士の繋がりを広げられます。<br>
                    <br>
                    また、マイページの掲示板一覧にて、発言したコンテンツの掲示板の管理が可能です。<br>
                </p>
                <div class="c-fixedPage__img c-fixedPage__img--col2">
                    <img src="../src/img/msg.png" alt="msg-img">
                </div>
            </div>
            <h4 class="c-fixedPage__title c-fixedPage__title--thr">いいね！・閲覧履歴機能</h4>
            <div class="c-fixedPage__col2">
                <p class="c-fixedPage__text c-fixedPage__text--col2">
                    いいね！・閲覧履歴機能により手軽にお気に入りの作品を閲覧、コメント投稿ができます。<br>
                    <br>
                    また、いいね数に応じて、TOPページのスライダーコンテンツに掲載されます。<br>
                    <br>
                    いい作品だけでなく、発想、着眼点、独創性、様々な観点から評価し、評価してもらいましょう！<br>
                </p>
                <div class="c-fixedPage__img c-fixedPage__img--col2">
                    <img src="../src/img/favorite.png" alt="favorite-img">
                </div>
            </div>
            <h4 class="c-fixedPage__title c-fixedPage__title--thr">利用料金</h4>
            <p class="c-fixedPage__text">
                利用料金は、一切掛かりません。 閲覧・投稿すべて無料です。
            </p>
            <a class="c-fixedPage__link" href="<?php echo SIGNUP; ?>">新規会員登録する</a>
        </div>
    </section>
</main>
<?php
require FOOTER;
?>
