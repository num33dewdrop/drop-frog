<?php
/*========================================
ログ
========================================*/
ini_set( 'log_errors', 'on' );
ini_set( 'error_log', BASE_URL . '/php.log' );

/*========================================
デバッグ
========================================*/
$development_flg = false;

function debug( $str ) {
	global $development_flg;
	if ( $development_flg ) {
		error_log( 'デバッグ：' . $str );
	}
}

/*========================================
セッション準備・セッション有効期限を延ばす
========================================*/
//セッションファイルの置き場を変更する（/var/tmp/以下に置くと30日は削除されない）
session_save_path( "/var/tmp/" );
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ１００分の１の確率で削除）
ini_set( 'session.gc_maxlifetime', 60 * 60 * 24 * 30 );
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set( 'session.cookie_lifetime', 60 * 60 * 24 * 30 );
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();

/*========================================
画面表示処理開始ログ吐き出し関数
========================================*/
function debugLogStart() {
	debug( '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面処理開始' );
	debug( 'セッションID：' . session_id() );
	debug( 'セッション変数の中身：' . print_r( $_SESSION, true ) );
	debug( '現在日時タイムスタンプ：' . time() );
	if ( ! empty( $_SESSION['login_date'] ) && ! empty( $_SESSION['login_limit'] ) ) {
		debug( 'ログイン有効期限日時タイムスタンプ：' . ( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );
	}
}
/*========================================
定数
========================================*/
const MSG_01 = '入力必須です。';
const MSG_02 = 'E-mailの形式で入力して下さい。';
const MSG_03 = '入力文字数が超過しました。';
const MSG_04 = 'そのE-mailは既に登録されています。';
const MSG_05 = '半角英数字で入力して下さい。';
const MSG_06 = '6文字以上で入力して下さい。';
const MSG_07 = 'エラーが発生しました。';
const MSG_08 = 'Password 再入力が合っていません。';
const MSG_09 = 'メールアドレスまたはパスワードが違います。';
const MSG_10 = '@で始まるTwitterアカウント名を入力して下さい';
const MSG_11 = '入力数が違います';
const MSG_12 = '電話番号の形式で入力して下さい。';
const MSG_13 = '郵便番号が存在しません。';
const MSG_14 = '年月日全て入力して下さい。';
const MSG_15 = '古いパスワードと同じです。';
const MSG_16 = '古いパスワードが違います。';
const MSG_17 = '認証キーが違います。';
const MSG_18 = '有効期限外です。再度最初からお試しください。';
const MSG_19 = '情報が更新されていません。<br>操作に誤りが無い場合はお問い合わせでご報告をお願いします。';
const MSG_20 = 'GitHubのアカウント名の登録がありません。';
const MSG_21 = '1枚以上登録してください。';
const MSG_22 = 'URLの形式で入力してください。';
const SUC_01 = 'ログイン情報を変更しました。';
const SUC_02 = 'プロフィールを変更しました。';
const SUC_03 = 'メールを送信しました。';
const SUC_04 = '登録しました。';
const SUC_05 = '個人情報を変更しました。';
const SUC_06 = '会員登録ありがとうございます。';
const SUC_08 = '公開しました。';
const SUC_09 = '削除しました。';
const SUC_10 = 'ご意見ありがとうございます。内容を確認し、より良いサービスを提供いたします。';
const SUC_11 = 'お問い合わせありがとうございます。内容を確認し、速やかに返答致します。';

/*========================================
グローバル変数
========================================*/
//エラーメッセージ用配列定義
$err_msg = array();
//性別データ
$sexData = array( '未選択', '男性', '女性', 'その他' );
//ソートデータ作品新着リスト
$sortPData = array( '新着順', '作品名昇順', '作品名降順' );
//ソートデータいいねリスト
$sortFData = array( '追加日順', '作品名昇順', '作品名降順' );
//ソートデータ閲覧履歴リスト
$sortHData = array( '閲覧日順', '閲覧回数昇順', '閲覧回数降順', '作品名昇順', '作品名降順' );
//ソートデータ作品更新リスト
$sortRData = array( '更新順', '作品名昇順', '作品名降順' );
//ソートデータ掲示板リスト
$sortBData = array( '更新順', '作品名昇順', '作品名降順', '作成者名昇順', '作成者名降順' );

/*========================================
関数
========================================*/
//========================================
//データベース準備・クエリ実行関数
//========================================
//DB接続
function dbConnect() {
	global $development_flg;
	if ( $development_flg ) {
		$dsn      = 'mysql:dbname=xxxxx;host=localhost;charset=utf8';
		$user     = 'root';
		$password = 'root';
		$options  = array(
			//EXCEPTIONなら、エラーが発生した時点で スクリプトの実行を停止させる
			//WARNINGなら、エラー内容だけ吐き出し、スクリプトの実行は停止させない
			PDO::ATTR_ERRMODE                  => PDO::ERRMODE_WARNING,
			//デフォルトフェッチモードを連想配列形式に設定
			PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_ASSOC,
			//バッファードクエリを使う（一度に結果セットを全て取得し、サーバー負荷を軽減）
			//SELECTで得た結果に対してもrowCountメソッドを使えるようにする
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
		);
	} else {
		$dsn      = 'mysql:dbname=xxxxx;host=localhost;charset=utf8';
		$user     = 'xxxxx';
		$password = 'xxxxx';
		$options  = array(
			//EXCEPTIONなら、エラーが発生した時点で スクリプトの実行を停止させる
			//WARNINGなら、エラー内容だけ吐き出し、スクリプトの実行は停止させない
			PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
			//デフォルトフェッチモードを連想配列形式に設定
			PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_ASSOC,
			//バッファードクエリを使う（一度に結果セットを全て取得し、サーバー負荷を軽減）
			//SELECTで得た結果に対してもrowCountメソッドを使えるようにする
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
		);
	}

	//PDOオブジェクト生成（DB接続）
	return new PDO( $dsn, $user, $password, $options );
}

//クエリ実行
function queryPost( $dbh, $sql, $data ) {
	//クエリー作成
	$stmt = $dbh->prepare( $sql );
	//プレースホルダに値をセットし、sql文を実行
	//execute() メソッドが成功した場合、true 失敗した場合は false
	if ( ! $stmt->execute( $data ) ) {
		global $err_msg;
		debug( 'クエリに失敗しました' );
		debug( '失敗したSQL：' . print_r( $stmt, true ) );
		$err_msg['common'] = MSG_07;

		return false;
	}
	debug( 'クエリ成功' );

	return $stmt;
}

//DBへの影響の確認
function dbAffect( $stmt ) {
	global $err_msg;
	if ( ! $stmt || $stmt->rowCount() === 0 ) {
		debug( 'クエリに失敗しました' );
		debug( '失敗したSQL：' . print_r( $stmt, true ) );
		$err_msg['common'] = MSG_19;

		return false;
	}

	return $stmt;
}

//========================================
//バリデーション関数
//========================================
//未入力
function validRequired( $str, $key ) {
	//0とnullはOKにし、空文字はNGとする
	if ( $str === '' ) {
		global $err_msg;
		$err_msg[ $key ] = MSG_01;
	}
}

//twitterアカウント形式
function validTwitter( $str, $key ) {
	//空データを許容
	if ( $str !== '' ) {
		// ユーザー名が@記号で始まるかどうかをチェックする正規表現パターン
		// \wはアルファベット、数字、アンダースコアを表す
		if ( ! preg_match( "/^@\w+/", $str ) ) {
			global $err_msg;
			$err_msg[ $key ] = MSG_10;
		}
	}
}

//URL形式
function validUrl( $str, $key ) {
	//空データを許容
	if ( $str !== '' ) {
		if ( ! filter_var( $str, FILTER_VALIDATE_URL ) ) {
			global $err_msg;
			$err_msg[ $key ] = MSG_22;
		}
	}
}

function validGitHub( $str , $key, $u_id ) {
	if ( $str !== '' ) {
		global $err_msg;
		try {
			//DB接続
			$dbh = dbConnect();
			//SQL文作成
			$sql  = 'SELECT git_hub FROM users WHERE id = :u_id AND delete_flg = 0';
			$data = array( ':u_id' => $u_id );
			//クエリ実行
			$stmt = queryPost( $dbh, $sql, $data );
			//クエリ結果の値を取得
			$result = $stmt->fetch( PDO::FETCH_ASSOC );
			if ( empty( array_shift( $result ) ) ) {
				debug('$result:'.print_r($result, true));
				debug('$result:'.print_r(array_shift( $result ), true));
				$err_msg[$key] = MSG_20;
			}
		} catch ( PDOException $e ) {
			error_log( 'エラー発生：' . $e->getMessage() );
			$err_msg['common'] = MSG_07;
		}
	}
}

//電話番号形式
function validTel( $str, $key ) {
	//空データを許容
	if ( $str !== '' ) {
		//電話番号から数字以外を除く
		$str = preg_replace( '/[^0-9]/ui', '', $str );
		if ( ! preg_match( "/0\d{1,4}\d{1,4}\d{4}/", $str ) ) {
			global $err_msg;
			$err_msg[ $key ] = MSG_12;
		}
	}
}

//郵便番号形式、APIから県・市・地域を返す
function validZip( $zip, $key ) {
	global $err_msg;
	//空データを許容
	if ( $zip !== '' ) {
		//APIからデータの取得。データがいらない時は真偽値
		$res = get_zipAddress( $zip );
		//データが存在しなければエラー表示
		if ( ! $res ) {
			$err_msg[ $key ] = MSG_13;
		}

		//データが必要ならリターン
		return $res;
	}

	return null;
}

//email形式
function validEmail( $str, $key ) {
	if ( ! filter_var( $str, FILTER_VALIDATE_EMAIL ) ) {
		global $err_msg;
		$err_msg[ $key ] = MSG_02;
	}
}

//email重複
function validEmailDup( $email ) {
	global $err_msg;
	try {
		//DB接続
		$dbh = dbConnect();
		//SQL文作成
		$sql  = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
		$data = array( ':email' => $email );
		//クエリ実行
		$stmt = queryPost( $dbh, $sql, $data );
		//クエリ結果の値を取得
		$result = $stmt->fetch( PDO::FETCH_ASSOC );
		if ( ! empty( array_shift( $result ) ) ) {
			$err_msg['email'] = MSG_04;
		}
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
		$err_msg['common'] = MSG_07;
	}
}

//最大文字数
function validMaxLen( $str, $key, $max = 255 ) {
	if ( mb_strlen( $str ) > $max ) {
		global $err_msg;
		$err_msg[ $key ] = MSG_03;
	}
}

//最小文字数
function validMinLen( $str, $key, $min = 6 ) {
	if ( mb_strlen( $str ) < $min ) {
		global $err_msg;
		$err_msg[ $key ] = MSG_06;
	}
}

//固定長
function validFixedLen( $str, $key, $length = 8 ) {
	if ( mb_strlen( $str ) !== $length ) {
		global $err_msg;
		$err_msg[ $key ] = MSG_11;
	}
}

//半角英数字
function validHalf( $str, $key ) {
	if ( ! preg_match( "/^[a-zA-Z0-9]+$/", $str ) ) {
		global $err_msg;
		$err_msg[ $key ] = MSG_05;
	}
}

//同値
function validMatch( $str1, $str2, $key ) {
	if ( $str1 !== $str2 ) {
		global $err_msg;
		$err_msg[ $key ] = MSG_08;
	}
}

//Emailバリデーションテンプレート
function validTempEmail( $str, $key ) {
	//email形式チェック
	validEmail( $str, $key );
	//email最大文字数チェック
	validMaxLen( $str, $key );
	//email重複チェック
	validEmailDup( $str );
}

//passwordバリデーションテンプレート
function validTempPass( $str, $key ) {
	//半角英数字チェック
	validHalf( $str, $key );
	//最大文字数チェック
	validMaxLen( $str, $key );
	//最小文字数チェック
	validMinLen( $str, $key );
}

//DBと差異判別
function diffDb( $str, $key, Closure $f, $other = null ) {
	global $dbFormData;
	if ( ! empty( $other ) ) {
		if ( $dbFormData[ $key ] !== $str ) {
			$f( $str, $key, $other );
		}
	} else {
		if ( $dbFormData[ $key ] !== $str ) {
			$f( $str, $key );
		}
	}
}

//========================================
//エラー表示関数
//========================================
//メッセージ表示
function getErrMsg( $key ) {
	global $err_msg;

	return ( ! empty( $err_msg[ $key ] ) ) ? $err_msg[ $key ] : '';
}

//クラス付与
function getErrClass( $key ) {
	global $err_msg;

	return ( ! empty( $err_msg[ $key ] ) ) ? 'c-err' : '';
}

//========================================
//DBデータ取得関数
//========================================
//退会理由カテゴリ
function getWithdrawalCategory() {
	global $err_msg;
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT id, reason FROM w_category WHERE delete_flg = 0';
		$data = array();
		$stmt = queryPost( $dbh, $sql, $data );

		//クエリ結果のデータを全レコード返却
		return $stmt ? $stmt->fetchAll() : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
		$err_msg['common'] = MSG_07;

		return false;
	}
}

//都道府県
function getPrefecture() {
	global $err_msg;
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT id, name FROM prefecture WHERE delete_flg = 0';
		$data = array();
		$stmt = queryPost( $dbh, $sql, $data );

		return $stmt ? $stmt->fetchAll() : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
		$err_msg['common'] = MSG_07;

		return false;
	}
}

//ユーザーデータ
function getUser( $u_id ) {
	global $err_msg;
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT id, nick_name, pic, introduction, twitter, blog, git_hub,
       			 first_name, last_name,kana_first_name, kana_last_name, birthday, sex, email, tel,
		         zip, prefecture_id, city, building, create_date
		         FROM users WHERE id = :u_id AND delete_flg = 0';
		$data = array( ':u_id' => $u_id );
		$stmt = queryPost( $dbh, $sql, $data );

		//クエリ結果のデータを１レコード返却
		return $stmt ? $stmt->fetch( PDO::FETCH_ASSOC ) : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
		$err_msg['common'] = MSG_07;

		return false;
	}
}

//ユーザーパスワードデータ
function getUserPass( $u_id ) {
	global $err_msg;
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT pass FROM users WHERE id = :u_id AND delete_flg = 0';
		$data = array( ':u_id' => $u_id );
		$stmt = queryPost( $dbh, $sql, $data );

		return $stmt ? $stmt->fetch( PDO::FETCH_ASSOC ) : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
		$err_msg['common'] = MSG_07;

		return false;
	}
}

//ユーザーページ用情報
function getPubUserInfo( $hash ) {
	try {
		$dbh    = dbConnect();
		$sql    = 'SELECT id, create_date FROM users WHERE delete_flg = 0';
		$data   = array();
		$stmt   = queryPost( $dbh, $sql, $data );
		$result = $stmt ? $stmt->fetchAll() : false;
		if ( ! empty( $result ) ) {
			foreach ( $result as $key => $val ) {
				if ( hash( "sha256", $val['id'] . $val['create_date'] ) === $hash ) {
					$sql  = 'SELECT id, nick_name, pic, introduction, twitter, blog, git_hub FROM users WHERE id = :u_id AND delete_flg = 0';
					$data = array( ':u_id' => $val['id'] );
					$stmt = queryPost( $dbh, $sql, $data );

					return $stmt ? $stmt->fetch( PDO::FETCH_ASSOC ) : false;
				}
			}
		}

		return false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

//商品編集用データ
function getProductEdit( $u_id, $p_id ) {
	global $err_msg;
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT * FROM product WHERE id = :p_id AND user_id = :u_id AND delete_flg = 0';
		$data = array( ':u_id' => $u_id, ':p_id' => $p_id );
		$stmt = queryPost( $dbh, $sql, $data );

		return $stmt ? $stmt->fetch( PDO::FETCH_ASSOC ) : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
		$err_msg['common'] = MSG_07;

		return false;
	}
}

//商品カテゴリデータ
function getProductCategory() {
	global $err_msg;
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT id, name, img, search_count FROM p_category WHERE delete_flg = 0 ORDER BY search_count DESC';
		$data = array();
		$stmt = queryPost( $dbh, $sql, $data );

		return $stmt ? $stmt->fetchAll() : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
		$err_msg['common'] = MSG_07;

		return false;
	}
}

//商品リスト
function getProductList( $category, $sort = 0, $currentMinNum = 0, $q = null, $span = 12 ) {
	debug( 'DBから商品情報を取得します' );
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT p.id, p.name, p.pic1 
				FROM product AS p
				LEFT JOIN p_category AS c ON p.category_id = c.id
				WHERE p.public_flg = 1 AND p.delete_flg = 0';
		$data = array();
		if ( ! empty( $category ) || ! empty( $q ) ) {
			$sql .= ' AND ';
			if ( ! empty( $category ) ) {
				$sql          .= 'p.category_id = :c_id';
				$data['c_id'] = $category;
				if ( ! empty( $q ) ) {
					$sql .= ' AND';
				}
			}
			if ( ! empty( $q ) ) {
				$textboxCondition = array();
				//テキストボックスの空白を半角スペースに置換し半角スペース区切りで配列に格納
				$textboxs = explode( " ", mb_convert_kana( $q, 's' ) );
				//SQL文に追加する字句の生成
				foreach ( $textboxs as $index => $value ) {
					$placeholder          = ":value" . $index;
					$textboxCondition[]   = "(p.name LIKE $placeholder OR p.detail LIKE $placeholder)";
					$data[ $placeholder ] = '%' . preg_replace( '/(?=[!_%])/', '', $value ) . '%';
				}
				//各Like条件を「OR」でつなぐ
				$textboxCondition = implode( ' OR ', $textboxCondition );
				$sql              .= $textboxCondition;

				//ローカルでは使用できた全文検索・・・xserverのMariaDB10.5ではngram非対応のため、断念。完全一致のみなら全文検索可能。
				//$sql .= ' (MATCH( p.name, p.detail ) AGAINST(:q) OR MATCH( c.name ) AGAINST(:q))';
			}
		}
		switch ( $sort ) {
			case 0:
				$sql .= ' ORDER BY p.create_date DESC';
				break;
			case 1:
				$sql .= ' ORDER BY p.name ASC';
				break;
			case 2:
				$sql .= ' ORDER BY p.name DESC';
				break;
		}
		$stmt              = queryPost( $dbh, $sql, $data );
		$rst['total']      = $stmt->rowCount(); //総レコード数
		$rst['total_page'] = ceil( $rst['total'] / $span ); //総ページ数
		if ( empty( $rst['total'] ) ) {
			return false;
		}

		// ページング用のSQL文追加
		$sql  .= ' LIMIT ' . $span . ' OFFSET ' . $currentMinNum;
		$stmt = queryPost( $dbh, $sql, $data );
		//クエリ結果のデータを全レコードを格納
		$rst['data'] = $stmt->fetchAll();

		return ! empty( $rst['data'] ) ? $rst : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

//お気に入り順商品リスト
function getProductListAndFavNum( $span = 12 ) {
	debug( 'DBから商品情報を取得します' );
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT p.id, p.name, p.pic1, count(f.product_id) AS f_count
				FROM product AS p
				LEFT JOIN favorite AS f ON p.id = f.product_id
				WHERE p.public_flg = 1 AND p.delete_flg = 0
				GROUP BY p.id ORDER BY f_count DESC LIMIT ' . $span;
		$data = array();
		$stmt = queryPost( $dbh, $sql, $data );
		//クエリ結果のデータを全レコードを格納
		$result = $stmt->fetchAll();

		return ! empty( $result ) ? $result : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

//商品詳細データ1レコード
function getProductDetail( $p_id, $history_flg = false ) {
	try {
		$dbh = dbConnect();
		$sql = 'SELECT p.id, p.name, p.category_id, p.detail, p.pic1, p.pic2, p.pic3, p.pic4, p.pic5, p.url, p.git_hub AS repository, p.user_id,
        c.name AS category, u.nick_name, u.git_hub AS git_name,u.create_date
		FROM product AS p 
		INNER JOIN users AS u ON p.user_id = u.id
		INNER JOIN p_category AS c ON p.category_id = c.id
		WHERE p.id = :p_id AND p.public_flg = 1 AND p.delete_flg = 0';
		if ( ! $history_flg ) {
			$sql .= ' AND p.delete_flg = 0 AND u.delete_flg = 0 AND c.delete_flg = 0';
		}
		$data = array( ':p_id' => $p_id );
		$stmt = queryPost( $dbh, $sql, $data );

		return $stmt ? $stmt->fetch( PDO::FETCH_ASSOC ) : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

//商品詳細用関連商品データ（カテゴリ商品５レコード）
function getProductRelation( $c_id ) {
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT id, name, pic1 FROM product 
                WHERE category_id = :c_id AND delete_flg = 0 AND public_flg = 1
                ORDER BY create_date DESC LIMIT 5';
		$data = array( ':c_id' => $c_id );
		$stmt = queryPost( $dbh, $sql, $data );

		return $stmt ? $stmt->fetchAll() : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

//出品商品データ
function getMyProducts( $u_id, $sort = 0, $currentMinNum = 0, $span = 12, $full_flg = false ) {
	try {
		$dbh = dbConnect();
		$sql = 'SELECT id AS p_id, name, pic1 FROM product
                WHERE user_id = :u_id AND delete_flg = 0 AND public_flg = 1';
		switch ( $sort ) {
			case 0:
				$sql .= ' ORDER BY create_date DESC';
				break;
			case 1:
				$sql .= ' ORDER BY name ASC';
				break;
			case 2:
				$sql .= ' ORDER BY name DESC';
				break;
		}

		$data              = array( ':u_id' => $u_id );
		$stmt              = queryPost( $dbh, $sql, $data );
		$rst['total']      = $stmt->rowCount(); //総レコード数
		$rst['total_page'] = ceil( $rst['total'] / $span ); //総ページ数
		if ( empty( $rst['total'] ) ) {
			return false;
		}

		// ページング用全件表示
		if ( $full_flg ) {
			//クエリ結果のデータを全レコードを格納
			$rst['data'] = $stmt->fetchAll();

			return ! empty( $rst['data'] ) ? $rst : false;
		}

		// ページング用のSQL文追加
		$sql  .= ' LIMIT ' . $span . ' OFFSET ' . $currentMinNum;
		$stmt = queryPost( $dbh, $sql, $data );
		//クエリ結果のデータを全レコードを格納
		$rst['data'] = $stmt->fetchAll();

		return ! empty( $rst['data'] ) ? $rst : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

//下書き商品データ
function getMyDrafts( $u_id, $sort = 0, $currentMinNum = 0, $span = 12 ) {
	try {
		$dbh = dbConnect();
		$sql = 'SELECT id AS p_id, name, update_date FROM product 
                WHERE user_id = :u_id AND delete_flg = 0 AND public_flg = 0';
		switch ( $sort ) {
			case 0:
				$sql .= ' ORDER BY update_date DESC';
				break;
			case 1:
				$sql .= ' ORDER BY name ASC';
				break;
			case 2:
				$sql .= ' ORDER BY name DESC';
				break;
		}
		$data              = array( ':u_id' => $u_id );
		$stmt              = queryPost( $dbh, $sql, $data );
		$rst['total']      = $stmt->rowCount(); //総レコード数
		$rst['total_page'] = ceil( $rst['total'] / $span ); //総ページ数
		if ( empty( $rst['total'] ) ) {
			return false;
		}

		// ページング用のSQL文追加
		$sql  .= ' LIMIT ' . $span . ' OFFSET ' . $currentMinNum;
		$stmt = queryPost( $dbh, $sql, $data );
		//クエリ結果のデータを全レコードを格納
		$rst['data'] = $stmt->fetchAll();

		return ! empty( $rst['data'] ) ? $rst : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

//マイページ用お気に入りデータ
function getMyLike( $u_id, $sort = 0, $currentMinNum = 0, $span = 12 ) {
	//例外処理
	try {
		// DBへ接続
		$dbh = dbConnect();
		// SQL文作成
		$sql = 'SELECT p.id AS p_id, p.name, p.pic1, u.id AS user_id, u.nick_name, u.create_date AS hash
       			FROM product AS p 
    			RIGHT JOIN favorite AS f ON p.id = f.product_id
       			INNER JOIN users AS u ON p.user_id = u.id
         		WHERE f.customer_user_id = :u_id AND p.delete_flg = 0 AND p.public_flg = 1 AND f.delete_flg = 0';
		switch ( $sort ) {
			case 0:
				$sql .= ' ORDER BY f.create_date DESC';
				break;
			case 1:
				$sql .= ' ORDER BY p.name ASC';
				break;
			case 2:
				$sql .= ' ORDER BY p.name DESC';
				break;
		}
		$data              = array( ':u_id' => $u_id );
		$stmt              = queryPost( $dbh, $sql, $data );
		$rst['total']      = $stmt->rowCount(); //総レコード数
		$rst['total_page'] = ceil( $rst['total'] / $span ); //総ページ数
		if ( empty( $rst['total'] ) ) {
			return false;
		}

		// ページング用のSQL文追加
		$sql  .= ' LIMIT ' . $span . ' OFFSET ' . $currentMinNum;
		$stmt = queryPost( $dbh, $sql, $data );
		//クエリ結果のデータを全レコードを格納
		$rst['data'] = $stmt->fetchAll();

		return ! empty( $rst['data'] ) ? $rst : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

//お気に入り数
function getFavoriteNum( $p_id ) {
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT count(*) FROM favorite WHERE product_id = :p_id AND delete_flg = 0';
		$data = array( ':p_id' => $p_id );
		$stmt = queryPost( $dbh, $sql, $data );

		return $stmt ? $stmt->fetchColumn() : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

//お知らせ
function getProductNews( $u_id ) {
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT n.id AS n_id, n.product_id, n.customer_user_id, n.notice_type, n.create_date, p.name, u.nick_name, u.create_date AS hash
				FROM p_notice AS n
				INNER JOIN product AS p ON n.product_id = p.id
				INNER JOIN users AS u ON n.customer_user_id = u.id
                WHERE p.user_id = :u_id AND n.customer_user_id != :u_id AND u.delete_flg = 0 AND p.delete_flg = 0
                ORDER BY n.create_date DESC';
		$data = array( ':u_id' => $u_id );
		$stmt = queryPost( $dbh, $sql, $data );

		return $stmt ? $stmt->fetchAll() : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

function isLike( $u_id, $p_id ) {
	debug( 'お気に入り情報があるか確認します。' );
	try {
		// DBへ接続
		$dbh = dbConnect();
		// SQL文作成
		$sql  = 'SELECT count(*) FROM favorite WHERE product_id = :p_id AND customer_user_id = :u_id';
		$data = array( ':u_id' => $u_id, ':p_id' => $p_id );
		// クエリ実行
		$stmt = queryPost( $dbh, $sql, $data );

		return (bool) $stmt->fetchColumn();
	} catch ( Exception $e ) {
		error_log( 'エラー発生:' . $e->getMessage() );

		return false;
	}
}

function getCountBrowsing( $u_id, $p_id ) {
	debug( '閲覧履歴情報があるか確認します。' );
	try {
		// DBへ接続
		$dbh = dbConnect();
		// SQL文作成
		$sql  = 'SELECT view_count FROM browsing_history WHERE product_id = :p_id AND customer_user_id = :u_id';
		$data = array( ':u_id' => $u_id, ':p_id' => $p_id );
		// クエリ実行
		$stmt = queryPost( $dbh, $sql, $data );

		return $stmt ? $stmt->fetch( PDO::FETCH_ASSOC ) : false;
	} catch ( Exception $e ) {
		error_log( 'エラー発生:' . $e->getMessage() );

		return false;
	}
}

function getMyBrowsingProduct( $u_id, $sort = 0, $currentMinNum = 0, $span = 12 ) {
	//例外処理
	try {
		// DBへ接続
		$dbh = dbConnect();
		// SQL文作成
		$sql = 'SELECT p.id AS p_id, p.name, p.pic1, u.id AS user_id, u.nick_name, u.create_date AS hash
       			FROM product AS p 
    			RIGHT JOIN browsing_history AS h ON p.id = h.product_id
       			INNER JOIN users AS u ON p.user_id = u.id
         		WHERE h.customer_user_id = :u_id AND p.delete_flg = 0 AND p.public_flg = 1 AND h.delete_flg = 0';
		switch ( $sort ) {
			case 0:
				$sql .= ' ORDER BY h.update_date DESC';
				break;
			case 1:
				$sql .= ' ORDER BY h.view_count ASC';
				break;
			case 2:
				$sql .= ' ORDER BY h.view_count DESC';
				break;
			case 3:
				$sql .= ' ORDER BY p.name ASC';
				break;
			case 4:
				$sql .= ' ORDER BY p.name DESC';
				break;
		}
		$data              = array( ':u_id' => $u_id );
		$stmt              = queryPost( $dbh, $sql, $data );
		$rst['total']      = $stmt->rowCount(); //総レコード数
		$rst['total_page'] = ceil( $rst['total'] / $span ); //総ページ数
		if ( empty( $rst['total'] ) ) {
			return false;
		}

		// ページング用のSQL文追加
		$sql  .= ' LIMIT ' . $span . ' OFFSET ' . $currentMinNum;
		$stmt = queryPost( $dbh, $sql, $data );
		//クエリ結果のデータを全レコードを格納
		$rst['data'] = $stmt->fetchAll();

		return ! empty( $rst['data'] ) ? $rst : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

//メッセージ情報
function getMsgs( $p_id ) {
	global $err_msg;
	try {
		//DB接続
		$dbh = dbConnect();
		//SQL文作成
		$sql  = 'SELECT m.id AS m_id, m.from_user_id, m.msg, m.create_date AS send_date,
       			u.pic, u.nick_name 
				FROM msg AS m
				RIGHT JOIN product AS p ON m.product_id = p.id
				RIGHT JOIN users AS u ON m.from_user_id = u.id
				WHERE p.id = :id AND p.delete_flg = 0 AND p.public_flg = 1 ORDER BY m.create_date ASC';
		$data = array( ':id' => $p_id );
		//クエリ実行
		$stmt = queryPost( $dbh, $sql, $data );

		//クエリ結果の値を取得
		return $stmt ? $stmt->fetchAll() : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
		$err_msg['common'] = MSG_07;

		return false;
	}
}

function getMyMsgsAndBoard( $u_id ) {
	global $err_msg;
	try {
		//DB接続
		$dbh = dbConnect();
		//SQL文作成
		$sql  = 'SELECT m.msg, m.create_date AS send_date,
       			p.id AS p_id, p.name, u.id AS customer_user_id, u.nick_name, u.create_date AS hash
				FROM msg AS m
			    INNER JOIN product AS p ON m.product_id = p.id
				INNER JOIN users AS u ON m.from_user_id = u.id
				WHERE p.user_id = :id AND p.delete_flg = 0 AND m.delete_flg = 0 AND u.delete_flg = 0
				ORDER BY m.create_date DESC';
		$data = array( ':id' => $u_id );
		//クエリ実行
		$stmt = queryPost( $dbh, $sql, $data );

		//クエリ結果の値を取得
		return $stmt ? $stmt->fetchAll() : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
		$err_msg['common'] = MSG_07;

		return false;
	}
}

//メッセージ件数
function getMsgNum( $p_id ) {
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT count(*) FROM msg WHERE product_id = :p_id AND delete_flg = 0';
		$data = array( ':p_id' => $p_id );
		$stmt = queryPost( $dbh, $sql, $data );

		return $stmt ? $stmt->fetchColumn() : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

//掲示板一覧情報
function getMySendBoards( $u_id, $sort = 0, $currentMinNum = 0, $span = 12 ) {
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT p.id AS p_id, p.name, u.id AS user_id, u.nick_name, u.create_date AS hash, m.update_date
	            FROM product AS p
	            LEFT JOIN users AS u ON p.user_id = u.id
	            INNER JOIN (SELECT product_id, from_user_id, MAX(update_date) AS update_date
				                FROM msg
				                GROUP BY product_id, from_user_id
				            ) AS m ON m.product_id = p.id
	            WHERE m.from_user_id = :u_id AND p.delete_flg = 0 AND p.public_flg = 1';
		$data = array( ':u_id' => $u_id );
		switch ( $sort ) {
			case 0:
				$sql .= ' ORDER BY m.update_date DESC';
				break;
			case 1:
				$sql .= ' ORDER BY p.name ASC';
				break;
			case 2:
				$sql .= ' ORDER BY p.name DESC';
				break;
			case 3:
				$sql .= ' ORDER BY u.nick_name ASC';
				break;
			case 4:
				$sql .= ' ORDER BY u.nick_name DESC';
				break;
		}
		$stmt              = queryPost( $dbh, $sql, $data );
		$rst['total']      = $stmt->rowCount(); //総レコード数
		$rst['total_page'] = ceil( $rst['total'] / $span ); //総ページ数
		if ( empty( $rst['total'] ) ) {
			return false;
		}

		// ページング用のSQL文追加
		$sql  .= ' LIMIT ' . $span . ' OFFSET ' . $currentMinNum;
		$stmt = queryPost( $dbh, $sql, $data );
		//クエリ結果のデータを全レコードを格納
		$rst['data'] = $stmt->fetchAll();

		return ! empty( $rst['data'] ) ? $rst : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

function getNews() {
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT n.id AS n_id, n.title, n.detail, n.update_date, c.name AS category_name 
       			FROM news AS n
       			LEFT JOIN n_category AS c ON n.n_category_id = c.id
       			WHERE n.public_flg = 1 AND n.delete_flg = 0 ORDER BY n.update_date DESC';
		$data = array();
		$stmt = queryPost( $dbh, $sql, $data );

		return $stmt ? $stmt->fetchAll() : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

function getNewsOne( $n_id ) {
	try {
		$dbh  = dbConnect();
		$sql  = 'SELECT n.id AS n_id, n.title, n.detail, n.update_date, c.name AS category_name 
       			FROM news AS n
       			LEFT JOIN n_category AS c ON n.n_category_id = c.id
       			WHERE n.id = :n_id AND n.delete_flg = 0 ORDER BY n.update_date DESC';
		$data = array( ':n_id' => $n_id );
		$stmt = queryPost( $dbh, $sql, $data );

		return $stmt ? $stmt->fetch( PDO::FETCH_ASSOC ) : false;
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );

		return false;
	}
}

//========================================
//DB挿入・更新・削除
//========================================
//商品公開（フラグ更新）
function publicProduct( $p_id, $u_id ) {
	try {
		//DB接続
		$dbh = dbConnect();
		//SQL文作成
		$sql  = 'UPDATE product SET public_flg = 1 WHERE id = :id AND user_id = :u_id AND delete_flg = 0';
		$data = array( ':id' => $p_id, ':u_id' => $u_id );
		//クエリ実行
		$stmt = queryPost( $dbh, $sql, $data );
		if ( dbAffect( $stmt ) ) {
			$_SESSION['msg_success'] = SUC_08;
			debug( 'マイページへ遷移します。' );
			header( "Location:" . MY_PAGE );
			exit();
		}
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
	}
}

//商品削除（掲示板があれば、論理削除）
function deleteProduct( $p_id, $u_id ) {
	try {
		//DB接続
		$dbh = dbConnect();
		//SQL文作成
		$sql  = 'SELECT count(*) FROM board WHERE product_id = :p_id';
		$data = array( ':p_id' => $p_id );
		$stmt = queryPost( $dbh, $sql, $data );
		if ( $stmt->fetchColumn() ) {
			$sql = 'UPDATE product SET delete_flg = 1 WHERE id = :p_id AND user_id = :u_id';
		} else {
			$sql = 'DELETE FROM product WHERE id = :p_id AND user_id = :u_id';
		}
		$data = array( ':p_id' => $p_id, ':u_id' => $u_id );
		//クエリ実行
		$stmt = queryPost( $dbh, $sql, $data );
		if ( dbAffect( $stmt ) ) {
			$_SESSION['msg_success'] = SUC_09;
			debug( 'マイページへ遷移します。' );
			header( "Location:" . MY_PAGE );
			exit();
		}
	} catch ( PDOException $e ) {
		error_log( 'エラー発生：' . $e->getMessage() );
	}
}

//========================================
//郵便番号処理関数
//========================================
//郵便番号からAPIリクエストURL取得
function getURL_address( $zip ) {
	//\-? は、ハイフンが0回または1回出現すること
	//() で括られた部分で、それぞれ1つのキャプチャグループになり、
	//$arr 配列にキャプチャされた文字列が格納される。
	$res = preg_match( '/(\d{3})\-?(\d{4})/', $zip, $arr );

	return ( $res == 0 ) ? false : ( "http://api.thni.net/jzip/X0401/JSON/{$arr[1]}/{$arr[2]}.js" );
}

//郵便番号から都道府県データの取得
function get_zipAddress( $zip, $ajax_flg = false ) {
	$item = array();
	//郵便番号から数字以外を除く
	$zip = preg_replace( '/[^0-9]/ui', '', $zip );
	//リクエストURL取得
	$url = getURL_address( $zip );
	if ( ! $url ) {
		return false;
	}
	//以下判定だけならいらない　return ($url)?:false;
	//変換実行
	$json = @file_get_contents( $url );
	if ( ! $json ) {
		return false;
	}
	//Ajaxで使う用JSON形式のままリターン
	if ( $ajax_flg ) {
		return $json;
	}

	//json文字列をphp配列に変換
	$arr = json_decode( $json );
	if ( ! isset( $arr->state ) ) {
		return false;
	}

	//配列に代入し、リターン
	$item['id']         = (string) $arr->state;
	$item['prefecture'] = (string) $arr->stateName;
	$item['city']       = (string) $arr->city;
	$item['street']     = (string) $arr->street;

	return ! empty( $item ) ? $item : false;
}

//========================================
//画像処理関数
//========================================
//画像アップロード
function uploadImg( $file, $key ) {
	global $err_msg;
	debug( '画像アップロード処理開始' );
	debug( 'FILE情報：' . print_r( $file, true ) );
	//$_FILES['error']の中に何かしら入っていて、かつ、数値型であれば画像と判断できる
	if ( isset( $file['error'] ) && is_int( $file['error'] ) ) {
		try {
			//バリデーション
			//$file['error'] の値を確認。配列内には「UPLOAD_ERR_OK」などの定数が入っている。
			//「UPLOAD_ERR_OK」などの定数はphpでファイルアップロード時に自動的に定義される。定数には値として0や1などの数値が入っている。
			switch ( $file['error'] ) {
				case UPLOAD_ERR_OK://OK
					break;
				case UPLOAD_ERR_NO_FILE://ファイル未選択
					throw new RuntimeException( 'ファイルが選択されていません。' );
				case UPLOAD_ERR_INI_SIZE://php.iniの最大サイズを超過
				case UPLOAD_ERR_FORM_SIZE://フォーム定義の最大サイズ超過
					throw new RuntimeException( 'ファイルサイズが大きすぎます' );
				default: //その他
					throw new RuntimeException( 'その他のエラーが発生しました' );
			}
			// $file['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
			// exif_imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_JPEG」などの定数を返す
			$type = @exif_imagetype( $file['tmp_name'] );
			if ( ! in_array( $type, [
				IMAGETYPE_GIF,
				IMAGETYPE_JPEG,
				IMAGETYPE_PNG
			], true ) ) {// 第三引数にはtrueを設定すると厳密にチェックしてくれるので必ずつける
				throw new RuntimeException( '画像形式が未対応です' );
			}
			//ファイルデータからSHA-1ハッシュを取ってファイル名を決定し、ファイルを保存する
			//ハッシュ化しておかないとアップロードされたファイル名そのままで保存してしまうと同じファイル名がアップロードされる可能性があり、
			//DBにパスを保存した場合、どっちの画像のパスなのか判断つかなくなってしまう
			//image_type_to_extension関数はファイルの拡張子を取得するもの
			$path = UPLOADS . sha1_file( $file['tmp_name'] ) . image_type_to_extension( $type );
			//$_FILES['tmp_name']（一時フォルダ）にある画像をパスを指定したアップロードファイルに移動
			if ( ! move_uploaded_file( $file['tmp_name'], $path ) ) {
				throw new RuntimeException( 'ファイル保存時にエラーが発生しました' );
			}
			//保存したファイルパスのパーミッション（権限）を変更する
			chmod( $path, 0644 );
			debug( 'ファイルは正常にアップロードされました' );
			debug( 'ファイルパス：' . $path );

			return $path;
		} catch ( RuntimeException $e ) {
			error_log( 'ランタイムエラー発生：' . $e->getMessage() );
			$err_msg[ $key ] = $e->getMessage();
		}
	}
	$err_msg[ $key ] = MSG_07;

	return false;
}

//画像表示
function showImg( $path, $c_id = null ) {
	return empty( $path ) ? BASE_NAME . 'src/img/noImage.png' : $path;
}

//========================================
//入力保持関数
//========================================
//誕生日入力保持
function getDataAndHoldBirthday( $key, $format ) {
	//ハイフン区切りの年月日の形式のフォーマット
	$format_str = '%Y-%m-%d';
	//入力保持関数でDBのデータorPOSTのデータの取得
	$date = getDataAndHold( 'birthday', $key );
	//DBのデータの場合：1970-01-01　の文字列
	//POSTのデータの場合（year）：1970　の文字列
	//strptime()でハイフン区切りの年月日の形式か判定
	//形式がマッチしていれば配列、アンマッチならfalseが返ってくる
	if ( ! strptime( $date, $format_str ) ) {
		//POSTのデータを返す
		return $date;
	} else {
		//strtotimeでUNIXタイムスタンプに変換し,
		//date()で必要な値を取得し、リターン
		//年なら$format = 'Y'
		return date( $format, strtotime( $date ) );
	}
}

//DB・POST入力保持
function getDataAndHold( $key, $key2 = '' ) {
	global $dbFormData;
	global $err_msg;
	if ( $key2 === '' ) {
		$key2 = $key;
	}
	//DBデータがある場合 数値の０は許容
	if ( isset( $dbFormData[ $key ] ) ) {
		//フォームにエラーがある場合
		if ( ! empty( $err_msg[ $key ] ) ) {
			//POST送信がある場合
			if ( isset( $_POST[ $key2 ] ) ) {
				//POSTのデータを保持
				return sanitize( $_POST[ $key2 ] );
				//POST送信がない場合（DBに登録されているデータなので、基本的には到達しない）
			} else {
				//DBのデータを保持
				return sanitize( $dbFormData[ $key ] );
			}
			//フォームにエラーがない場合
		} else {
			//POST送信があり、DBと違う場合
			if ( isset( $_POST[ $key2 ] ) && $dbFormData[ $key ] !== $_POST[ $key2 ] ) {
				//POSTのデータを保持
				return sanitize( $_POST[ $key2 ] );
				//POST送信がない、または、DBと同じ値の場合
			} else {
				//DBのデータを保持
				return sanitize( $dbFormData[ $key ] );
			}
		}
		//DBデータがない場合
	} else {
		//POST送信がある場合
		if ( isset( $_POST[ $key2 ] ) ) {
			//POSTのデータを保持
			return sanitize( $_POST[ $key2 ] );
		}

		return null;
	}
}

//POST・GET入力保持
function getMethodHold( $key, $check_flg = false, $method_flg = false ) {
	$method = $method_flg ? $_GET : $_POST;
	if ( $check_flg ) {
		return ( ! empty( $method[ $key ] ) ) ? 'checked' : '';
	}

	return ( isset( $method[ $key ] ) ) ? sanitize( $method[ $key ] ) : null;
}

//========================================
//ページネーション関数
//========================================
//ページング
// $currentPageNum : 現在のページ数
// $totalPageNum : 総ページ数
// $link : 検索用GETパラメータリンク
// $pageColNum : ページネーション表示数
function paginationProcess( $currentPageNum, $totalPageNum, $pageColNum ) {
	// 現在のページが、総ページ数と同じ　かつ　総ページ数が表示項目数以上なら、左にリンク４個出す
	if ( $currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum ) {
		$minPageNum = $currentPageNum - 4;
		$maxPageNum = $currentPageNum;
		// 現在のページが、総ページ数の１ページ前なら、左にリンク３個、右に１個出す
	} elseif ( $currentPageNum == ( $totalPageNum - 1 ) && $totalPageNum >= $pageColNum ) {
		$minPageNum = $currentPageNum - 3;
		$maxPageNum = $currentPageNum + 1;
		// 現ページが2の場合は左にリンク１個、右にリンク３個だす。
	} elseif ( $currentPageNum == 2 && $totalPageNum >= $pageColNum ) {
		$minPageNum = $currentPageNum - 1;
		$maxPageNum = $currentPageNum + 3;
		// 現ページが1の場合は左に何も出さない。右に５個出す。
	} elseif ( $currentPageNum == 1 && $totalPageNum >= $pageColNum ) {
		$minPageNum = $currentPageNum;
		$maxPageNum = 5;
		// 総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを１に設定
	} elseif ( $totalPageNum < $pageColNum ) {
		$minPageNum = 1;
		$maxPageNum = $totalPageNum;
		// それ以外は左に２個出す。
	} else {
		$minPageNum = $currentPageNum - 2;
		$maxPageNum = $currentPageNum + 2;
	}

	return array( 'minPageNum' => $minPageNum, 'maxPageNum' => $maxPageNum );
}

function pagination( $currentPageNum, $totalPageNum, $link = '', $pageColNum = 5 ) {
	$pageNum = paginationProcess( $currentPageNum, $totalPageNum, $pageColNum );
	extract( $pageNum );

	echo '<div class="c-pageNation">';
	echo '<ul class="c-pageNation__list">';
	if ( $currentPageNum != 1 ) {
		echo '<li class="c-pageNation__item c-pageNation__item--nav"><a href="?p=1' . $link . '" class="c-pageNation__link"><i class="fa-solid fa-angle-left"></i></a></li>';
	}
	for ( $i = $minPageNum; $i <= $maxPageNum; $i ++ ) {
		echo '<li class="c-pageNation__item"><a href="?p=' . $i . $link . '" class="c-pageNation__link ';
		if ( $currentPageNum == $i ) {
			echo 'c-pageNation__link--active';
		}
		echo '">' . $i . '</a></li>';
	}
	if ( $currentPageNum != $maxPageNum && $maxPageNum > 1 ) {
		echo '<li class="c-pageNation__item c-pageNation__item--nav"><a href="?p=' . $totalPageNum . $link . '" class="c-pageNation__link"><i class="fa-solid fa-angle-right"></i></a></li>';
	}
	echo '</ul>';
	echo '</div>';
}

//ajax用
function pagination2( $currentPageNum, $totalPageNum, $pageColNum = 5 ) {
	$pageNum = paginationProcess( $currentPageNum, $totalPageNum, $pageColNum );
	extract( $pageNum );

	$str = '<div class="c-pageNation">';
	$str .= '<ul class="c-pageNation__list">';
	if ( $currentPageNum != 1 ) {
		$str .= '<li class="c-pageNation__item c-pageNation__item--nav"><button class="c-pageNation__link js-pagination" value="1"><i class="fa-solid fa-angle-left"></i></button></li>';
	}
	for ( $i = $minPageNum; $i <= $maxPageNum; $i ++ ) {
		$str .= '<li class="c-pageNation__item"><button class="c-pageNation__link js-pagination ';
		if ( $currentPageNum == $i ) {
			$str .= 'c-pageNation__link--active';
		}
		$str .= '" value="' . $i . '">' . $i . '</button></li>';

	}
	if ( $currentPageNum != $maxPageNum && $maxPageNum > 1 ) {
		$str .= '<li class="c-pageNation__item c-pageNation__item--nav"><button class="c-pageNation__link js-pagination" value="' . $totalPageNum . '"><i class="fa-solid fa-angle-right"></i></button></li>';
	}
	$str .= '</ul>';
	$str .= '</div>';

	return $str;
}

//ページング用GETパラメータ生成
function appendPageParam( $arr_del_key = array() ) {
	if ( ! empty( $_GET ) ) {
		$str = '';
		foreach ( $_GET as $key => $val ) {
			if ( ! in_array( $key, $arr_del_key, true ) ) {
				$str .= '&' . $key . '=' . $val;
			}
		}

		return $str;
	}
}

//========================================
//GETパラメータ
//========================================
//GETパラメータ付与
//$del_key : 付与から取り除きたいGETパラメーター
function appendLinkParam( $arr_del_key = array() ) {
	if ( ! empty( $_GET ) ) {
		$str = '?';
		foreach ( $_GET as $key => $val ) {
			if ( ! in_array( $key, $arr_del_key, true ) ) {
				$str .= $key . '=' . $val . '&';
			}
		}

		//最後の＆を取り除いてリターン
		return mb_substr( $str, 0, - 1, "UTF-8" );
	}
}

//================================
// ログイン認証
//================================
function authLogin() {
	//ログインしている場合
	if ( ! empty( $_SESSION['login_date'] ) ) {
		//現在日時が最終ログイン日時＋有効期限を超えていた場合
		if ( time() > ( $_SESSION['login_date'] + $_SESSION['login_limit'] ) ) {
			//セッションの削除
			session_destroy();
			//ログインページへ
			$_SESSION['login_key'] = $_SERVER['REQUEST_URI'];
			header( 'Location:' . LOGIN );
			exit();
			//ログイン期限以内の場合
		} else {
			//最終ログイン日時を現在時刻に更新
			$_SESSION['login_date'] = time();
			//現在実行中のスクリプトファイルがlogin.phpの場合
			if ( basename( $_SERVER['PHP_SELF'] ) === 'login.php' ) {
				debug( 'マイページへ遷移します' );
				header( 'Location:' . MY_PAGE );
				exit();
			}
		}
		//ログインしていない場合
	} else {
		//現在実行中のスクリプトファイルがlogin.phpでない場合
		if ( basename( $_SERVER['PHP_SELF'] ) !== 'login.php' ) {
			debug( 'ログインページへ遷移します' );
			$_SESSION['login_key'] = $_SERVER['REQUEST_URI'];
			header( 'Location:' . LOGIN );
			exit();
		}
	}
}

//Ajax用
function isLogin() {
	// ログインしている場合
	if ( ! empty( $_SESSION['login_date'] ) ) {
		debug( 'ログイン済みユーザーです。' );

		// 現在日時が最終ログイン日時＋有効期限を超えていた場合
		if ( ( $_SESSION['login_date'] + $_SESSION['login_limit'] ) < time() ) {
			debug( 'ログイン有効期限オーバーです。' );

			// セッションを削除（ログアウトする）
			session_destroy();

			return false;
		} else {
			debug( 'ログイン有効期限以内です。' );

			return true;
		}

	} else {
		debug( '未ログインユーザーです。' );

		return false;
	}
}


//========================================
//その他関数
//========================================
// サニタイズ
function sanitize( $str ) {
	return htmlspecialchars( $str, ENT_QUOTES );
}

//sessionを１回だけ取得
function getSessionFlash( $key ) {
	if ( ! empty( $_SESSION[ $key ] ) ) {
		//一時保管
		$data = $_SESSION[ $key ];
		//セッションの中身を空にする
		$_SESSION[ $key ] = '';

		//一時保管していたデータをリターンすることで、sessionを１回だけ取得
		return $data;
	}

	return '';
}

//年月日結合
function createDate( $year, $month, $day ) {
	//情報が全てある場合年月日結合
	if ( ! empty( $year ) && ! empty( $month ) && ! empty( $day ) ) {
		//str_pad()で０埋めし、yyyy-mm-dd形式に変換
		return $year . '-' . str_pad( $month, 2, 0, STR_PAD_LEFT ) . '-' . str_pad( $day, 2, 0, STR_PAD_LEFT );
		//情報が全て無い場合
	} else if ( empty( $year ) && empty( $month ) && empty( $day ) ) {
		return null;
		//情報はあるが、情報が欠けている場合
	} else {
		global $err_msg;
		$err_msg['birthday'] = MSG_14;

		return null;
	}
}

// メール送信
function sendMail( $to, $subject, $comment ) {
	if ( ! empty( $to ) && ! empty( $subject ) && ! empty( $comment ) ) {
		//文字化けしないように設定（お決まりパターン）
		mb_language( "Japanese" ); //現在使っている言語を設定する
		mb_internal_encoding( "UTF-8" ); //内部の日本語をどうエンコーディング（機械が分かる言葉へ変換）するかを設定

		// 送信元
		$from = "DROP FROG <info@drop-frog.com>";
		// 送信元メールアドレス
		$from_mail = "info@drop-frog.com";
		// 送信者名
		$from_name = "DROP FROG";

		// 送信者情報の設定
		$header = "Content-Type: text/plain \r\n";//メール形式
		$header .= "Return-Path: " . $from_mail . " \r\n";//送信先メールアドレスが受け取り不可の場合に、エラー通知のいくメールアドレス
		$header .= "From: " . $from . " \r\n";//送信者の名前（または組織名）とメールアドレス
		$header .= "Sender: " . $from . " \r\n";//送信者の名前（または組織名）とメールアドレス
		$header .= "Reply-To: " . $from_mail . " \r\n";//受け取った人に表示される返信の宛先
		$header .= "Organization: " . $from_name . " \r\n";//送信者名（または組織名）
		$header .= "X-Sender: " . $from_mail . " \r\n";//送信者のメールアドレス
		$header .= "X-Priority: 3 \r\n";//メールの重要度を表す
		//メールを送信（送信結果はtrueかfalseで返ってくる）
		$result = mb_send_mail( $to, $subject, $comment, $header );
		//送信結果を判定
		if ( $result ) {
			debug( 'メールを送信しました。' );
		} else {
			debug( '【エラー発生】メールの送信に失敗しました。' );
		}
	}
}

//ランダムキー生成
function mkRandKey( $length = 8 ) {
	//静的変数として宣言
	//staticがなければ関数が呼び出されるたびに、
	//この文字列が再度初期化されるため、ランダムなキーの生成にかかる時間が長くなる
	//static宣言をすれば、$chars 変数の初期化が1回だけ実行され、その後は保存された値が使用されるため、
	//ランダムなキーを生成するための関数のパフォーマンスが向上
	static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
	$str = '';
	for ( $i = 0; $i < $length; ++ $i ) {
		$str .= $chars[ mt_rand( 0, 61 ) ];
	}

	return $str;
}

