<?php
//共通変数・関数ファイルを読込み
require( '../configPath.php' );
require FUNC;
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debug( '『『『『『　テンプレートページ　『『『『『' );
debug( '『『『『『『『『『『『『『『『『『『『『『' );
debugLogStart();

?>

<!--　タイトル・検索件数　-->
<script type="text/template" id="headLeftTemplate">
    <% console.log('total : ',total); %>
    <h3 class="c-thrTitle js-subTitlePos"><%= title %></h3>
    <% if(total > 0) {%>
    <p class="c-contentsNum"><%= currentMinNum + 1 %> - <%= currentMaxNum %> 件 表示　/ <%= total %> 件中</p>
    <% } %>
</script>

<!--　ドロップダウンボタン　-->
<script type="text/template" id="headRightTemplate">
    <button class="c-triangle c-triangle--right">
        <span class="c-dropDown__current js-dropDown__current"><%= currentItem %></span>
    </button>
    <ul class="c-dropDown__list js-dropDown__target"><%= list %></ul>
</script>

<!--　ドロップダウンリスト　-->
<script type="text/template" id="sortListTemplate">
    <li class="c-dropDown__item js-dropDown__item">
        <button class="c-dropDown__btn" value="<%= index %>"><%= value %></button>
    </li>
</script>

<!-- 掲載作品コンテンツ　-->
<script type="text/template" id="salesTemplate">
    <li class="c-contents__item c-contents__item--col3">
        <div class="c-contents__itemContainer">
            <a href="<?php echo REGISTER_PRODUCT . '?p_id='; ?><%= p_id %>" class="c-contents__img">
                <img src="<%= pic1 %>" alt="<%= name %>">
            </a>
            <div class="c-contents__body">
                <p class="c-contents__info">
                    <span class="c-contents__label">作品名</span>
                    <%= name %>
                </p>
                <p class="c-contents__info">
                    <span class="c-contents__label">いいね</span>
                    <span>
                        <i class="fa-solid fa-heart c-contents__icon"></i>
                        <span><%= favoriteNum %></span>
                    </span>
                </p>
                <p class="c-contents__info">
                    <span class="c-contents__label">掲示板</span>
                    <a href="<?php echo MSG; ?>?p_id=<%= p_id %>">
                        <i class="fa-regular fa-comments c-contents__icon"></i>
                        <span><%= msgNum %></span>
                    </a>
                </p>
            </div>
        </div>
    </li>
</script>

<!-- いいね・閲覧履歴一覧コンテンツ -->
<script type="text/template" id="favoriteTemplate">
    <li class="c-contents__item c-contents__item--col3">
        <div class="c-contents__itemContainer">
            <a href="<?php echo PRODUCT_DETAIL . '?p_id='; ?><%= p_id %>" class="c-contents__img">
                <img src="<%= pic1 %>" alt="<%= name %>">
            </a>
            <div class="c-contents__body">
                <p class="c-contents__info">
                    <span class="c-contents__label">作品名</span>
                    <%= name %>
                </p>
                <p class="c-contents__info">
                    <span class="c-contents__label">作成者</span>
                    <a href="<?php echo PUB_USER_PAGE; ?><%= hashId %>">
                        <% if(nick_name === null || !nick_name.length) { %>
                        no name
                        <% }else { %>
                        <%= nick_name %>
                        <% } %>
                    </a>
                </p>
                <p class="c-contents__info">
                    <span class="c-contents__label">いいね</span>
                    <% if(favorite_flg) { %>
                    <button class="c-contents__favorite js-favorite js-favorite--active" data-pid="<%= p_id %>">
                        <i class="fa-solid fa-heart c-contents__icon" aria-hidden="true"></i>
                        <span><%= favoriteNum %></span>
                    </button>
                    <% }else { %>
                    <button class="c-contents__favorite js-favorite" data-pid="<%= p_id %>">
                        <i class="fa-regular fa-heart c-contents__icon" aria-hidden="true"></i>
                        <span><%= favoriteNum %></span>
                    </button>
                    <% }%>
                </p>
                <p class="c-contents__info">
                    <span class="c-contents__label">掲示板</span>
                    <a href="<?php echo MSG; ?>?p_id=<%= p_id %>">
                        <i class="fa-regular fa-comments c-contents__icon"></i>
                        <span><%= msgNum %></span>
                    </a>
                </p>
            </div>
        </div>
    </li>
</script>

<!--　表示作品リスト　-->
<script type="text/template" id="contentWrapTemplate">
    <div class="c-contents">
        <ul class="c-contents__list c-contents__list--wrap c-contents__list--col3"><%= contents %></ul>
    </div>
    <% if(total > 15) { %>
    <%= pagination %>
    <% } %>
</script>

<!--　表示作品無し　-->
<script type="text/template" id="noContentTemplate">
    <div class="c-contents c-contents--none"><%= title %>はありません。</div>
</script>

<!-- 掲示板・下書きリスト -->
<script type="text/template" id="tableTemplate">
    <div class="c-table c-table--scroll">
        <table class="c-table__main c-table__main--pageNation c-table__main--scroll">
            <thead>
            <% if(s_id === 'draftProduct'){ %>
            <tr>
                <th>編集</th>
                <th>作品名</th>
                <th>最終編集日</th>
                <th>公開設定</th>
                <th>削除</th>
            </tr>
            <% } else { %>
            <tr>
                <th>送る</th>
                <th>作成者</th>
                <th>作品名</th>
                <th>最終更新日</th>
            </tr>
            <% } %>
            </thead>
            <%= contents %>
        </table>
    </div>
    <% if(total > 15) { %>
    <%= pagination %>
    <% } %>
</script>

<!--　掲示板コンテンツ　-->
<script type="text/template" id="boardTemplate">
    <tr>
        <td>
            <a href="<?php echo MSG . '?p_id='; ?><%= p_id %>">
                <i class="fa-regular fa-comments c-contents__tableIcon"></i>
            </a>
        </td>
        <td>
            <a href="<?php echo PUB_USER_PAGE; ?><%= hashId %>">
                <% if(nick_name === null || !nick_name.length) { %>
                no name
                <% }else { %>
                <%= nick_name %>
                <% } %>
            </a>
        </td>
        <td><%= name %></td>
        <td><%= update_date %></td>
    </tr>
</script>

<!--　下書きコンテンツ　-->
<script type="text/template" id="draftsTemplate">
    <tr>
        <td>
            <a href="<?php echo REGISTER_PRODUCT . '?p_id='; ?><%= p_id %>">
                <i class="fa-regular fa-pen-to-square c-contents__tableIcon"></i>
            </a>
        </td>
        <td><%= name %></td>
        <td><%= update_date %></td>
        <td>
            <button class="js-draftEdit" name="edit_p_id" value="<%= p_id %>">公開する</button>
        </td>
        <td>
            <button class="js-draftEdit" name="delete_p_id" value="<%= p_id %>"><i
                        class="fa-solid fa-trash-can c-contents__tableIcon"></i></button>
        </td>
    </tr>
</script>