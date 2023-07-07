<?php
$dbQueryCategoryData = getProductCategory();
?>
<header id="header" class="l-header">
    <h1 class="l-header__title"><a href="<?php echo HOME; ?>" class="l-header__logo">DROP FROG</a></h1>
    <div class="l-header__search">
        <form action="<?php echo PRODUCT_LIST; ?>" method="get" class="c-searchForm">
            <input type="hidden" name="<?php if ( ! empty( getMethodHold( 'sort', false, true ) ) )
				echo 'sort' ?>" id="js-hiddenSort" value="<?php echo getMethodHold( 'sort', false, true ); ?>"
                   autocomplete="off">
            <div class="c-select c-triangle c-triangle--right">
                <select name="c_id">
                    <option class="js-insertCategoryTarget" value="0">すべてのカテゴリー</option>
					<?php
					if ( ! empty( $dbQueryCategoryData ) ) {
						foreach ( $dbQueryCategoryData as $option ) {
							if ( getMethodHold( 'c_id', false, true ) === $option['id'] ) {
								echo '<option class="js-insertCategoryTarget" value="' . $option['id'] . '" selected>' . $option['name'] . '</option>';
							} else {
								echo '<option class="js-insertCategoryTarget" value="' . $option['id'] . '">' . $option['name'] . '</option>';
							}
						}
					}
					?>
                </select>
            </div>
            <div class="c-searchForm__queryField">
                <input type="search" name="q" id="q" placeholder="何をお探しですか？" class="js-searchField"
                       value="<?php echo getMethodHold( 'q', false, true ); ?>" autocomplete="off">
            </div>
            <div class="c-searchForm__submit">
                <input type="submit" class="fas js-onSearch" value="&#xf002;">
            </div>
        </form>
    </div>
    <nav class="l-header__nav c-spSlideMenu js-toggleSpMenuTarget">
        <ul class="l-header__list">
			<?php if ( empty( $_SESSION['user_id'] ) ) { ?>
                <li class="l-header__item"><a class="l-header__link" href="<?php echo SIGNUP; ?>">新規登録</a></li>
                <li class="l-header__item"><a class="l-header__link" href="<?php echo LOGIN; ?>">ログイン</a></li>
			<?php } else { ?>
                <li class="l-header__item"><a class="l-header__link" href="<?php echo MY_PAGE; ?>">マイページ</a></li>
                <li class="l-header__item"><a class="l-header__link" href="<?php echo REGISTER_PRODUCT; ?>">投稿する</a>
                </li>
			<?php } ?>
        </ul>
    </nav>
</header>
