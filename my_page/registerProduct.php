<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『　作品登録・編集ページ　『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
authLogin();

//画面表示用データ取得
//================================
//GETデータを格納
debug( 'GET情報：' . print_r( $_GET, true ) );
$p_id = ! empty( $_GET['p_id'] ) ? $_GET['p_id'] : '';
//DBから作品データ取得
$dbFormData = ! empty( $p_id ) ? getProductEdit( $_SESSION['user_id'], $p_id ) : '';
//DBからカテゴリデータを取得
$dbCategoryData = getProductCategory();
//新規登録画面か編集画面か判別用フラグ
$edit_flg = ! empty( $dbFormData );
debug( '作品データ：' . print_r( $dbFormData, true ) );

$delete_flg = false;
// パラメータ改ざんチェック
//================================
//GETパラメータはあるが、改ざんされている（URLをいじくった）場合、
//正しい作品データが取れないのでマイページへ遷移させる
if ( ! empty( $p_id ) && empty( $dbFormData ) ) {
	debug( 'GETパラメータの作品IDが違います。マイページへ遷移します。' );
	header( "Location:" . MY_PAGE );
	exit();
}

//登録・編集ボタンでpost送信されていた場合
if ( ! empty( $_POST['register'] ) ) {
	debug( 'POST送信があります。' );
	debug( 'POST情報：' . print_r( $_POST, true ) );
	debug( 'FILE情報：' . print_r( $_FILES, true ) );
	for ( $i = 1; $i <= 5; $i ++ ) {
		if ( ! empty( $_POST[ 'hiddenPic' . $i ] ) ) {
			$delete_flg = true;
		}
	}
	//変数に作品情報を代入
	//画像処理
	for ( $i = 1; $i <= 5; $i ++ ) {
		//画像をアップロードし、パスを格納
		${"pic" . $i} = ! empty( $_FILES[ 'pic' . $i ]['name'] ) ? uploadImg( $_FILES[ 'pic' . $i ], 'pic' . $i ) : null;
		//画像をPOSTしてない、かつ、削除ボタンが押されていた場合
		if ( empty( ${"pic" . $i} ) && $delete_flg ) {
			//削除後の値を代入
			${"pic" . $i} = $_POST[ 'hiddenPic' . $i ];
			//画像をPOST、または、削除ボタンが押されていなかった場合
		} else {
			//画像をPOSTしてないが既にDBに登録されている場合
			( empty( ${"pic" . $i} ) && ! empty( $dbFormData[ 'pic' . $i ] ) ) ? ${"pic" . $i} = $dbFormData[ 'pic' . $i ] : ${"pic" . $i};
		}
	}
	$name        = $_POST['name'];//必須
	$category_id = $_POST['category_id'];//必須
	$detail      = getMethodHold( 'detail' );//何も入力がない場合nullで登録
	$url         = getMethodHold( 'url' );//何も入力がない場合nullで登録
	$git_hub     = getMethodHold( 'git_hub' );//何も入力がない場合nullで登録
	$public_flg  = $_POST['public_flg'];//初期入力あり
	//新規登録の場合
	if ( ! $edit_flg ) {
		//未入力
		validRequired( $name, 'name' );
		validRequired( $category_id, 'category_id' );
		//更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
	} else {
		//未入力
		diffDb( $name, 'name', Closure::fromCallable( 'validRequired' ) );
		diffDb( $category_id, 'category_id', Closure::fromCallable( 'validRequired' ) );
	}
	debug( 'エラーメッセージ　：　' . print_r( $err_msg, true ) );
	if ( empty( $err_msg ) ) {
		if ( ! $edit_flg ) {
			//最大文字数
			validMaxLen( $name, 'name' );
			validMaxLen( $detail, 'detail', 500 );
			validMaxLen( $url, 'url' );
			validMaxLen( $git_hub, 'git_hub' );
			//url形式
			validUrl( $url, 'url' );
			validGitHub( $git_hub, 'git_hub', $_SESSION['user_id'] );
		} else {
			//最大文字数
			diffDb( $name, 'name', Closure::fromCallable( 'validMaxLen' ) );
			diffDb( $detail, 'detail', Closure::fromCallable( 'validMaxLen' ), 500 );
			diffDb( $url, 'url', Closure::fromCallable( 'validMaxLen' ) );
			diffDb( $git_hub, 'git_hub', Closure::fromCallable( 'validMaxLen' ) );
			//url形式
			diffDb( $url, 'url', Closure::fromCallable( 'validUrl' ) );
			diffDb( $git_hub, 'git_hub', Closure::fromCallable( 'validGitHub' ), $_SESSION['user_id'] );
		}
		if ( empty( $err_msg ) ) {
			debug( 'バリデーション0K' );
			//公開設定がONなら公開日を代入
			$public_date = $public_flg ? date( 'Y-m-d H:i:s' ) : null;
			try {
				$dbh  = dbConnect();
				$data = array(
					':name'        => $name,
					':category_id' => $category_id,
					':detail'      => $detail,
					':pic1'        => $pic1,
					':pic2'        => $pic2,
					':pic3'        => $pic3,
					':pic4'        => $pic4,
					':pic5'        => $pic5,
					':url'         => $url,
					':git_hub'     => $git_hub,
					':user_id'     => $_SESSION['user_id'],
					':public_flg'  => $public_flg,
					':public_date' => $public_date
				);
				if ( ! $edit_flg ) {
					$sql  = 'INSERT INTO product(name, category_id, detail, pic1, pic2, pic3, pic4, pic5, url, git_hub, user_id, public_flg, public_date, create_date)
                            VALUES(:name, :category_id, :detail, :pic1, :pic2, :pic3, :pic4, :pic5, :url, :git_hub, :user_id, :public_flg, :public_date, :create_date)';
					$data = array_merge( $data, array( ':create_date' => date( 'Y-m-d H:i:s' ) ) );
				} else {
					$sql  = 'UPDATE product SET name = :name, category_id = :category_id, detail = :detail, pic1 = :pic1,  pic2 = :pic2, pic3 = :pic3, pic4 = :pic4, pic5 = :pic5,
                                               url = :url, git_hub = :git_hub, public_flg = :public_flg, public_date = :public_date
                            WHERE id = :p_id AND user_id = :user_id AND delete_flg = 0';
					$data = array_merge( $data, array( ':p_id' => $p_id ) );
				}
				debug( '流し込みデータ：' . print_r( $data, true ) );
				$stmt = queryPost( $dbh, $sql, $data );
				if ( dbAffect( $stmt ) ) {
					$_SESSION['msg_success'] = SUC_04;
					debug( print_r( $_SESSION, true ) );
					debug( 'マイページへ遷移します。' );
					header( "Location:" . MY_PAGE );
					exit();
				}
			} catch ( PDOException $e ) {
				error_log( 'エラー発生：' . $e->getMessage() );
				$err_msg['common'] = MSG_07;
			}
		}
	}
}

//削除ボタンでpostされていた場合
if ( ! empty( $_POST['delete'] ) ) {
	deleteProduct( $p_id, $_SESSION['user_id'] );
}
debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );

$title = ! $edit_flg ? '作品登録' : '作品編集';
require HEAD;
require HEADER;
?>
    <main id="main" class="l-main">
        <section class="c-container">
            <h2 class="c-subTitle c-subTitle--col2">マイページ</h2>
            <div class="c-container__col2">
				<?php require SIDEBAR; ?>
                <div class="c-container__main">
                    <div class="c-container__body">
                        <h3 class="c-thrTitle c-thrTitle--center"><?php echo ! $edit_flg ? '投稿する' : '編集する'; ?></h3>
                        <form action="" method="post" class="c-editForm" enctype="multipart/form-data">
                            <div class="c-areaMsg c-areaMsg--common">
								<?php echo getErrMsg( 'common' ); ?>
                            </div>
                            <div class="c-editForm__imgContainer">
								<?php for ( $i = 1; $i <= 5; $i ++ ) { ?>
                                    <div class="c-editForm__imgItem js-showDropTarget" style="<?php if ( $i > 1 )
										echo 'display:none;' ?>">
                                        <div class="c-editForm__spInner">
                                            <label for="pic<?php echo $i; ?>" class="js-areaDrop">画像<?php echo $i; ?>
                                                <div class="c-editForm__dropzone">
                                                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760">
                                                    <input type="file" id="pic<?php echo $i; ?>"
                                                           name="pic<?php echo $i; ?>" class="js-inputFile">
                                                    <img src="<?php echo getDataAndHold( 'pic' . $i ); ?>"
                                                         alt="areaDrop" class="js-prevTarget"
                                                         style="<?php if ( empty( getDataAndHold( 'pic' . $i ) ) ) {
														     echo 'display: none';
													     } ?>">
                                                </div>
                                            </label>
                                            <button class="c-removeImg js-removeImg" style="display: none">削除</button>
                                            <span class="c-areaMsg"><?php echo getErrMsg( 'pic' . $i ); ?></span>
                                        </div>
                                    </div>
								<?php } ?>
                            </div>
                            <div class="c-editForm__caution">
                                <p>※最大5枚まで登録できます。</p>
                                <p>※JPEG・PNG・GIF形式の画像を登録してください。</p>
                                <p>※画像は正方形で登録することを推奨しています。</p>
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="name" class="<?php echo getErrClass( 'name' ); ?>">
                                    作品名
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'name' ); ?></span>
                                </label>
                                <input type="text" id="name" class="c-editForm__input" name="name"
                                       value="<?php echo getDataAndHold( 'name' ); ?>">
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--half">
                                <label for="category_id">
                                    カテゴリ
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'category_id' ); ?></span>
                                </label>
                                <div class="c-editForm__select c-triangle c-triangle--right <?php echo getErrClass( 'category_id' ); ?>">
                                    <select name="category_id" id="category_id">
                                        <option value>選択してください</option>
										<?php foreach ( $dbCategoryData as $option ) {
											if ( getDataAndHold( 'category_id' ) === $option['id'] ) {
												echo '<option value="' . $option['id'] . '" selected>' . $option['name'] . '</option>';
											} else {
												echo '<option value="' . $option['id'] . '">' . $option['name'] . '</option>';
											}
										} ?>
                                    </select>
                                </div>
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="detail" class="<?php echo getErrClass( 'detail' ); ?>">
                                    詳細
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'detail' ); ?></span>
                                </label>
                                <textarea name="detail" id="detail" cols="30" rows="10"
                                          class="c-editForm__textarea js-counterText"><?php echo getDataAndHold( 'detail' ); ?></textarea>
                                <p class="c-counter"><span
                                            class="js-showCounter"><?php echo mb_strlen( getDataAndHold( 'detail' ) ); ?></span>/30,000
                                </p>
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="url" class="<?php echo getErrClass( 'url' ); ?>">
                                    URL
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'url' ); ?></span>
                                </label>
                                <input type="text" id="url" class="c-editForm__input" name="url"
                                       value="<?php echo getDataAndHold( 'url' ); ?>" placeholder="https://example.com">
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="git_hub" class="<?php echo getErrClass( 'git_hub' ); ?>">
                                    GitHub公開リポジトリ名
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'git_hub' ); ?></span>
                                </label>
                                <input type="text" id="git_hub" class="c-editForm__input" name="git_hub"
                                       value="<?php echo getDataAndHold( 'git_hub' ); ?>" placeholder="example-name">
                                <p class="c-editForm__caution c-editForm__caution--left">※『リポジトリ名』のみ入力して下さい。</p>
                                <p class="c-editForm__caution c-editForm__caution--left">
                                    ※『リポジトリ名』登録前に、プロフィール編集にてGitHubアカウントを登録して下さい。</p>
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--half">
                                <span class="label">公開設定</span>
                                <ul class="c-editForm__radio c-flex">
									<?php for ( $i = 0; $i <= 1; $i ++ ) { ?>
                                        <li>
                                            <label for="public_flg<?php echo $i; ?>" class="c-radio">
                                                <input type="radio" id="public_flg<?php echo $i; ?>" name="public_flg"
                                                       value="<?php echo $i; ?>" <?php if ( (int) getDataAndHold( 'public_flg' ) === $i )
													echo 'checked' ?>>
                                                <span class="c-radio__outer"><span class="c-radio__inner"></span></span>
												<?php echo $i ? '公開する' : '下書きにする'; ?>
                                            </label>
                                        </li>
									<?php } ?>
                                </ul>
                            </div>
                            <div class="c-flex c-flex--spaceAro u-paddingT-l">
                                <input type="hidden" name="hiddenPic1" id="hiddenPic1" value="">
                                <input type="hidden" name="hiddenPic2" id="hiddenPic2" value="">
                                <input type="hidden" name="hiddenPic3" id="hiddenPic3" value="">
                                <input type="hidden" name="hiddenPic4" id="hiddenPic4" value="">
                                <input type="hidden" name="hiddenPic5" id="hiddenPic5" value="">
                                <input type="submit" value="削除する" class="c-btn c-btn--alert js-alert" name="delete"
                                       style="<?php if ( ! $edit_flg ) {
									       echo 'display:none;';
								       } ?>">
                                <input type="submit" value="<?php echo ! $edit_flg ? '登録する' : '編集する'; ?>"
                                       class="c-btn" name="register">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?php
require FOOTER;
?>