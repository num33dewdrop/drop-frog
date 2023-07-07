<!doctype html>
<html lang=ja>
<head>
    <meta charset="UTF-8">
    <meta name="author" content="DROP FROG">
    <meta name="description" content="『DROP FROG』は様々な駆け出しクリエイターの作品を展示し、評価してもらえるコミュニティーサイトです。">
    <!--  数字の並びの自動リンク化を無効  -->
    <meta name="format-detection" content="telephone=no">
    <!--  viewport-fit=cover:  Safe Area外の余白となっている領域にも、サイトのデザインが反映  -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <!--  TwitterなどのSNSでシェアされた時のタグ  -->
    <meta property="og:site_name" content="DROP FROG">
    <meta property="og:type" content="website">
    <meta property="og:title" content="DROP FROG">
    <meta property="og:description" content="『DROP FROG』は様々な駆け出しクリエイターの作品を展示し、評価してもらえるコミュニティーサイトです。">
    <meta property="og:url" content="https://drop-frog.com">
    <meta property="og:image" content="https://drop-frog.com/src/img/sebastian.jpg">
    <!--  画像小バージョン  -->
    <meta name="twitter:card" content="summary">
    <meta property="twitter:title" content="DROP FROG">
    <title><?php if ( ! empty( $title ) ) {
			echo $title . '　|　';
		} ?>DROP FROG</title>
    <link rel="stylesheet" href="<?php echo CSS; ?>">
    <link rel="icon" href="../src/img/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sigmar&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8398227d24.js" crossorigin="anonymous"></script>
</head>
<body>
<p class="p-slideMsg js-showMsg"><?php echo getSessionFlash( 'msg_success' ); ?></p>
<p class="p-slideMsg p-slideMsg--err js-showMsg"><?php echo getSessionFlash( 'msg_err' ); ?></p>
<div class="c-spMenu js-toggleSpMenu">
    <span></span>
    <span></span>
    <span></span>
</div>