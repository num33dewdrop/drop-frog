<footer id="footer" class="l-footer">
    <div class="l-footer__info">
        <div class="l-footer__left">
            <a href="<?php echo HOME; ?>" class="l-footer__logo">DROP FROG</a>
        </div>
        <div class="l-footer__right">
            <nav class="l-footer__nav">
                <ul class="l-footer__list">
                    <li class="l-footer__item"><a class="l-footer__link"
                                                  href="<?php echo MEMBER_DETAIL; ?>">無料会員登録について</a></li>
                    <li class="l-footer__item"><a class="l-footer__link" href="<?php echo TERMS_OF_USE ?>">利用規約</a>
                    </li>
                    <li class="l-footer__item"><a class="l-footer__link"
                                                  href="<?php echo PRIVACY_POLICY; ?>">プライバシーポリシー</a></li>
                    <li class="l-footer__item"><a class="l-footer__link" href="<?php echo CONTACT; ?>">お問い合わせ</a></li>
                </ul>
            </nav>
            <p>DROP FROGではサービス向上のため利用者様からのご意見を募集し、改善していきます。</p>
            <p class="u-marginB-m">※こちらのフォームで送信された場合、送信者の特定をしません。ご返信が必要な場合は「お問い合せ」よりご連絡ください。</p>
            <form action="../common/opinion.php" method="post">
                <textarea class="l-footer__msg u-marginB-m" name="opinion" id="opinion" cols="30" rows="10"
                          placeholder="改善して欲しい点・追加して欲しい機能やオプションなどご意見をお聞かせください"></textarea>
                <input type="submit" class="c-btn c-btn--right" value="送信する">
            </form>
        </div>
    </div>
    <p class="l-footer__copyright">©︎ DROP FROG. All Rights Reserved.</p>
</footer>
<script src="<?php echo JS ?>"></script>
</body>
</html>