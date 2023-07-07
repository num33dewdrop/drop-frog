<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『　個人情報編集ページ　『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();
authLogin();
//DBからユーザーデータを取得
$dbFormData = getUser( $_SESSION['user_id'] );
//都道府県データ
$prefectureData = getPrefecture();

//POST送信されていた場合
if ( ! empty( $_POST ) ) {
	debug( 'POST送信があります。' );
	debug( 'POST情報：' . print_r( $_POST, true ) );
	//変数に情報を代入
	//未入力チェックが無いので未入力の時に空文字を入れないようにする。DBはの初期値がnullのものはnullのまま
	$last_name       = getMethodHold( 'last_name' );
	$first_name      = getMethodHold( 'first_name' );
	$kana_last_name  = getMethodHold( 'kana_last_name' );
	$kana_first_name = getMethodHold( 'kana_first_name' );
	$year            = $_POST['year'];
	$month           = $_POST['month'];
	$day             = $_POST['day'];
	$sex             = ! empty( $_POST['sex'] ) ? $_POST['sex'] : 0;//初期入力0
	$tel             = getMethodHold( 'tel' );
	$zip             = getMethodHold( 'zip' );
	$prefecture_id   = getMethodHold( 'prefecture_id' );
	$city            = getMethodHold( 'city' );
	$building        = getMethodHold( 'building' );

	//DBの情報と入力情報が異なる場合にバリデーションを行う
	diffDb( $last_name, 'last_name', Closure::fromCallable( 'validMaxLen' ) );
	diffDb( $first_name, 'first_name', Closure::fromCallable( 'validMaxLen' ) );
	diffDb( $kana_last_name, 'kana_last_name', Closure::fromCallable( 'validMaxLen' ) );
	diffDb( $kana_first_name, 'kana_first_name', Closure::fromCallable( 'validMaxLen' ) );
	diffDb( $tel, 'tel', Closure::fromCallable( 'validTel' ) );
	diffDb( $city, 'city', Closure::fromCallable( 'validMaxLen' ) );
	diffDb( $building, 'building', Closure::fromCallable( 'validMaxLen' ) );
	//年月日結合・情報が欠けている場合エラー
	$birthday = createDate( $year, $month, $day );
	//一応勉強のためにデータ取得（使わない。jsではデータを使用）
	$zipRes = ( $dbFormData['zip'] !== (string) $zip ) ? validZip( $zip, 'zip' ) : array();

	if ( empty( $err_msg ) ) {
		debug( 'バリデーションOK' );
		try {
			$dbh  = dbConnect();
			$sql  = 'UPDATE users SET last_name = :last_name, first_name = :first_name,
                 kana_last_name = :kana_last_name, kana_first_name = :kana_first_name,
                 birthday = :birthday, sex = :sex, tel = :tel,
                 zip = :zip, prefecture_id = :prefecture_id, city = :city, building = :building
                 WHERE id = :u_id';
			$data = array(
				':last_name'       => $last_name,
				':first_name'      => $first_name,
				':kana_last_name'  => $kana_last_name,
				':kana_first_name' => $kana_first_name,
				':birthday'        => $birthday,
				':sex'             => $sex,
				':tel'             => $tel,
				':zip'             => $zip,
				':prefecture_id'   => $prefecture_id,
				':city'            => $city,
				':building'        => $building,
				':u_id'            => $dbFormData['id']
			);
			$stmt = queryPost( $dbh, $sql, $data );
			if ( dbAffect( $stmt ) ) {
				$_SESSION['msg_success'] = SUC_05;
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
debug( '画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' );

$title = '個人情報編集';
require HEAD;
require HEADER;
?>
    <main id="main" class="l-main">
        <section class="c-container">
            <h2 class="c-subTitle c-subTitle--col2">マイページ</h2>
            <div class="c-container__col2">
				<?php require SIDEBAR; ?>
                <div class="c-container__main">
                    <div class="c-container__body c-container__body--middleSize">
                        <h3 class="c-thrTitle c-thrTitle--center">個人情報・編集</h3>
                        <form action="" method="post" class="c-editForm" enctype="multipart/form-data">
                            <div class="c-areaMsg c-areaMsg--common">
								<?php echo getErrMsg( 'common' ); ?>
                            </div>
                            <div class="c-flex">
                                <div class="c-editForm__inputContainer c-editForm__inputContainer--half">
                                    <label for="last_name" class="<?php echo getErrClass( 'last_name' ); ?> ">
                                        お名前 （姓）
                                        <span class="c-areaMsg"><?php echo getErrMsg( 'last_name' ); ?></span>
                                    </label>
                                    <input type="text" id="last_name" class="c-editForm__input" name="last_name"
                                           value="<?php echo getDataAndHold( 'last_name' ); ?>">
                                </div>
                                <div class="c-editForm__inputContainer c-editForm__inputContainer--half">
                                    <label for="first_name" class="<?php echo getErrClass( 'first_name' ); ?> ">
                                        お名前（名）
                                        <span class="c-areaMsg"><?php echo getErrMsg( 'first_name' ); ?></span>
                                    </label>
                                    <input type="text" id="first_name" class="c-editForm__input" name="first_name"
                                           value="<?php echo getDataAndHold( 'first_name' ); ?>">
                                </div>
                            </div>
                            <div class="c-flex">
                                <div class="c-editForm__inputContainer c-editForm__inputContainer--half">
                                    <label for="kana_last_name" class="<?php echo getErrClass( 'kana_last_name' ); ?> ">
                                        お名前（セイ）
                                        <span class="c-areaMsg"><?php echo getErrMsg( 'kana_last_name' ); ?></span>
                                    </label>
                                    <input type="text" id="kana_last_name" class="c-editForm__input"
                                           name="kana_last_name"
                                           value="<?php echo getDataAndHold( 'kana_last_name' ); ?>">
                                </div>
                                <div class="c-editForm__inputContainer c-editForm__inputContainer--half">
                                    <label for="kana_first_name"
                                           class="<?php echo getErrClass( 'kana_first_name' ); ?> ">
                                        お名前（メイ）
                                        <span class="c-areaMsg"><?php echo getErrMsg( 'kana_first_name' ); ?></span>
                                    </label>
                                    <input type="text" id="kana_first_name" class="c-editForm__input"
                                           name="kana_first_name"
                                           value="<?php echo getDataAndHold( 'kana_first_name' ); ?>">
                                </div>
                            </div>
                            <span class="c-areaMsg"><?php echo getErrMsg( 'birthday' ); ?></span>
                            <div class="c-flex">
                                <div class="c-editForm__inputContainer c-editForm__inputContainer--third">
                                    <label for="year">生年月日（年）</label>
                                    <div class="c-editForm__select c-triangle c-triangle--right">
                                        <select name="year" id="year" class="js-yearInsert">
                                            <option value>-</option>
											<?php
											for ( $i = date( 'Y' ) - 100; $i <= date( 'Y' ); $i ++ ) {
												if ( (int) getDataAndHoldBirthday( 'year', 'Y' ) === $i ) {
													echo '<option value="' . $i . '" selected>' . $i . '</option>';
												} else {
													echo '<option value="' . $i . '">' . $i . '</option>';
												}
											}
											?>
                                        </select>
                                    </div>
                                </div>
                                <div class="c-editForm__inputContainer c-editForm__inputContainer--third">
                                    <label for="month">生年月日（月）</label>
                                    <div class="c-editForm__select c-triangle c-triangle--right">
                                        <select name="month" id="month" class="js-monthInsert">
                                            <option value="">-</option>
											<?php
											for ( $i = 1; $i <= 12; $i ++ ) {
												if ( (int) getDataAndHoldBirthday( 'month', 'm' ) === $i ) {
													echo '<option value="' . $i . '" selected>' . $i . '</option>';
												} else {
													echo '<option value="' . $i . '">' . $i . '</option>';
												}
											}
											?>
                                        </select>
                                    </div>
                                </div>
                                <div class="c-editForm__inputContainer c-editForm__inputContainer--third">
                                    <label for="day">生年月日（日）</label>
                                    <div class="c-editForm__select c-triangle c-triangle--right">
                                        <select name="day" id="day">
                                            <option value="">-</option>
											<?php
											for ( $i = 1; $i <= 31; $i ++ ) {
												if ( (int) getDataAndHoldBirthday( 'day', 'd' ) === $i ) {
													echo '<option value="' . $i . '" selected>' . $i . '</option>';
												} else {
													echo '<option value="' . $i . '">' . $i . '</option>';
												}
											}
											?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <span class="label">性別</span>
                                <ul class="c-editForm__radio c-flex">
									<?php foreach ( $sexData as $key => $value ) { ?>
                                        <li>
                                            <label for="sex<?php echo $key ?>" class="c-radio">
                                                <input type="radio" id="sex<?php echo $key ?>" name="sex"
                                                       value="<?php echo $key ?>" <?php if ( (int) getDataAndHold( 'sex' ) === $key )
													echo 'checked' ?> >
                                                <span class="c-radio__outer"><span class="c-radio__inner"></span></span>
												<?php echo $value ?>
                                            </label>
                                        </li>
									<?php } ?>
                                </ul>
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <span class="label">E-mail</span>
                                <p class="c-editForm__fixed c-flex c-flex--spaceBet">
                                    <span><?php echo $dbFormData['email']; ?></span>
                                </p>
                                <p class="u-fontSize-s u-align-R">E-mailの編集は<a
                                            href="<?php echo LOGIN_INFO; ?>">こちら</a>から</p>
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--half">
                                <label for="tel" class="<?php echo getErrClass( 'tel' ); ?> ">
                                    電話番号
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'tel' ); ?></span>
                                </label>
                                <input type="text" id="tel" class="c-editForm__input" name="tel"
                                       value="<?php echo getDataAndHold( 'tel' ); ?>">
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--half">
                                <label for="zip" class="<?php echo getErrClass( 'zip' ); ?> ">
                                    郵便番号
                                    <span class="c-areaMsg js-removeText"><?php echo getErrMsg( 'zip' ); ?></span>
                                </label>
                                <input type="text" id="prefecture" class="c-editForm__input js-autoInsert" name="zip"
                                       value="<?php echo getDataAndHold( 'zip' ); ?>">
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--half">
                                <label for="prefecture_id">都道府県</label>
                                <div class="c-editForm__select c-triangle c-triangle--right">
                                    <select name="prefecture_id" id="prefecture_id" class="js-autoInsertPref">
                                        <option value="0"></option>
										<?php
										foreach ( $prefectureData as $option ) {
											if ( getDataAndHold( 'prefecture_id' ) === $option['id'] ) {
												echo '<option value="' . $option['id'] . '" selected>' . $option['name'] . '</option>';
											} else {
												echo '<option value="' . $option['id'] . '">' . $option['name'] . '</option>';
											}
										}
										?>
                                    </select>
                                </div>
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="city" class="<?php echo getErrClass( 'city' ); ?> ">
                                    市町村・番地
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'city' ); ?></span>
                                </label>
                                <input type="text" id="city" class="c-editForm__input js-autoInsertCity" name="city"
                                       value="<?php echo getDataAndHold( 'city' ); ?>">
                            </div>
                            <div class="c-editForm__inputContainer c-editForm__inputContainer--full">
                                <label for="building" class="<?php echo getErrClass( 'building' ); ?> ">
                                    建物名・部屋番号
                                    <span class="c-areaMsg"><?php echo getErrMsg( 'building' ); ?></span>
                                </label>
                                <input type="text" id="building" class="c-editForm__input" name="building"
                                       value="<?php echo getDataAndHold( 'building' ); ?>">
                            </div>
                            <div class="c-flex c-flex--justCenter u-paddingT-l">
                                <input type="submit" value="登録する" class="c-btn">
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