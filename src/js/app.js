import $ from 'jquery';
import _ from 'underscore';

$(function () {
    const development_flg = false;

    /*================================================================
    フッター最下部
    ================================================================*/
    let $ftr = $('#footer');
    if (window.innerHeight > $ftr.offset().top + $ftr.outerHeight()) {
        $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;'});
    }

    /*================================================================
    自己紹介文表示
    ================================================================*/
    $(".js-showText").on('click', function () {
        $(this).toggleClass('js-showText--active');
    });

    /*================================================================
    テキストエリアカウント
    ================================================================*/
    $('.js-counterText').on('keyup', function () {
        let $countView = $(this).siblings().find('.js-showCounter');
        $countView.html($(this).val().length);
    });

    /*================================================================
    都道府県・市町村・地域表示
    ================================================================*/
    $('.js-autoInsert').on('keyup', function () {
        $.ajax({
            type: 'post',
            url: 'ajaxZip.php',//送信先のパス。パスはこのjsファイルから見た時のどこか。「相対パス」
            dataType: 'text',
            data: {//サーバーに送るデータ
                zip: $(this).val()
            }
        }).done(function (data, status) {//.done：Ajax通信が終わったら実行　第一引数：サーバーから帰ってきた値、第二引数：通信が成功したかの文字列"success"
            console.log(data);
            console.log(status);
            if (data !== '') {
                let zipData = JSON.parse(data),
                    $pref = $('.js-autoInsertPref'),
                    $city = $('.js-autoInsertCity'),
                    $errText = $('.js-removeText');
                $pref.val(zipData.state);
                $city.val(zipData.city + zipData.street);
                $errText.text('');
            }
        }).fail(function (msg) {
            console.log('Ajax Error：' + msg);
        });
    });


    /*================================================================
    全件表示
    ================================================================*/
    $(".js-showFullBtn").on('click', function () {
        let $this = $(this),
            $parent = $this.parents(".js-showFullContainer"),
            $target = $parent.find(".js-showFullTarget"),
            $targetPos = $parent.find(".js-subTitlePos");
        $this.toggleClass("js-showFullBtn--active");
        $target.toggleClass("js-showFullTarget--active");
        if ($this.hasClass("js-showFullBtn--active")) {
            $this.text('閉じる');
        } else {
            $this.text('もっと見る');
            $("body , html").scrollTop($targetPos.offset().top - 100);
        }
    });

    /*================================================================
    削除確認
    ================================================================*/
    $(document).on('click', ".js-alert", function () {
        if (!confirm('削除しますか？')) return false;
    });

    /*================================================================
    githubアカウント削除確認
    ================================================================*/
    const DeleteGitHub = (() => {
        let $input = $('.js-deleteInput'),
            $submit = $('.js-submitBtn'),
            initVal = $input.val(),
            delete_flg = false,
            currentVal = '';
        return {
            init: () => {
                $input.on('input', function () {
                    currentVal = $(this).val();
                    // 入力フィールドが空になった場合にPOST送信ボタンを無効化
                    delete_flg = (currentVal === '' && initVal.length);
                });
                // POST送信ボタンのクリックイベント
                $submit.click(function () {
                    // 入力フィールドが空の場合は送信をキャンセル
                    if (delete_flg) {
                        if (!confirm('GitHubアカウント名を削除すると、登録作品のレポジトリ名の登録も消去されます。')) {
                            return false;
                        }
                    }
                });
            }
        }
    })();
    DeleteGitHub.init();

    /*================================================================
    通知メッセージ表示
    ================================================================*/
    const SlideNotice = (() => {
        let $jsShowMsg = $('.js-showMsg'),
            msg = $jsShowMsg.text();
        return {
            init: () => {
                //前後にある全角スペースと半角スペースを取り除いた長さで判定
                if (msg.replace(/^[\s　]+|[\s　]+$/g, "").length) {
                    $jsShowMsg.slideToggle('slow');
                    setTimeout(function () {
                        $jsShowMsg.slideToggle('slow');
                    }, 5000);
                }
            }
        }
    })();
    SlideNotice.init();

    /*================================================================
    SPメニュー
    ================================================================*/
    const SpSlideMenu = (() => {
        /*==================================
        property
        ==================================*/
        let local = window.location,
            $menuTarget = $(".js-toggleSpMenuTarget"),
            $sidebarTarget = $(".js-toggleSpSidebarTarget"),
            $target = null,
            str = development_flg ? '/drop-frog/my_page/' : '/my_page/';
        /*==================================
        method
        ==================================*/
        let getDir = (place, n) => {
                return place.pathname.replace(new RegExp("(?:\\\/+[^\\\/]*){0," + ((n || 0) + 1) + "}$"), "/");
            },
            hideSidebar = ($target, key) => {
                $('.js-spSidebarHide').on('click', function () {
                    $sidebarTarget.removeClass('c-spSlideMenu--active');
                    if ($target !== undefined) $target.removeClass(key);
                });
            };
        return {
            init: () => {
                $('.js-toggleSpMenu').on('click', function () {
                    let $this = $(this);
                    if (getDir(local) === str) {
                        $target = $sidebarTarget;
                        $this.css('z-index', 5);
                    } else {
                        $target = $menuTarget;
                    }
                    $this.toggleClass('js-toggleSpMenu--active');
                    $target.toggleClass('c-spSlideMenu--active');
                    hideSidebar($this, 'js-toggleSpMenu--active');
                });

                $('.js-toggleSpSidebar').on('click', function () {
                    $sidebarTarget.toggleClass('c-spSlideMenu--active');
                    hideSidebar();
                });
            }
        }
    })();
    SpSlideMenu.init();

    /*================================================================
    imgスライダー
    ================================================================*/
    const Slider = (() => {//即時実行関数　returnのオブジェクトが変数に入る
        /*==================================
        property
        ==================================*/
        const SHOW_ITEM = 3,
            DURATION = 500;
        //variable
        let currentNum = 0,
            $sliderList = $('.js-sliderList'),
            $sliderItems = $sliderList.children(),
            sliderItemNum = $sliderItems.length,
            containerWidth = $('.c-slider__container').innerWidth(),
            sliderItemWidth = $($sliderItems[0]).outerWidth(true),
            widthPercent = (sliderItemWidth * 100 / containerWidth),
            initPos = -widthPercent * SHOW_ITEM,
            lastPos = -widthPercent * sliderItemNum,

            screenWidth = $(window).width(),
            screenHeight = $(window).height(),
            str = development_flg ? '/drop-frog/public/' : '/public/',

            intervalId = null,

            touchStartX = 0,
            touchEndX = 0;
        /*==================================
        method
        ==================================*/
        let next = () => {
                currentNum++;
                $sliderList.animate({left: '-=' + widthPercent + '%'}, DURATION);
            },
            prev = () => {
                currentNum--;
                $sliderList.animate({left: '+=' + widthPercent + '%'}, DURATION);
            },
            appendItem = () => {
                for (let i = 0; i < SHOW_ITEM; i++) {
                    $sliderItems.eq(i).clone().appendTo($sliderList);
                    $sliderItems.eq(-1 - i).clone().prependTo($sliderList);
                }
                $sliderList.animate({left: initPos + '%'}, 0);
            },
            slidePrev = () => {//前へ　メソッドの定義
                if (currentNum > -SHOW_ITEM) {
                    prev();
                } else if (currentNum === -SHOW_ITEM) {
                    currentNum = sliderItemNum - SHOW_ITEM;
                    $sliderList.animate({left: lastPos + '%'}, 0);
                    prev();
                }
            },
            slideNext = () => {//次へ　メソッドの定義
                if (currentNum < sliderItemNum) {
                    next();
                } else if (currentNum === sliderItemNum) {
                    currentNum = 0;
                    $sliderList.animate({left: initPos + '%'}, 0);
                    next();
                }
            },
            autoSlide = () => {
                intervalId = setInterval(() => {
                    slideNext();
                }, 5000)
            },
            //スワイプ機能
            handleSwipe = () => {
                // スワイプ方向の判定
                if (touchStartX - touchEndX > 50) {
                    // 左にスワイプ（次へ）
                    if (!$sliderList.is(':animated')) slideNext();
                } else if (touchEndX - touchStartX > 50) {
                    // 右にスワイプ（前へ）
                    if (!$sliderList.is(':animated')) slidePrev();
                }
                clearInterval(intervalId);
                autoSlide();
            },
            handleTouchStart = e => {
                touchStartX = e.changedTouches[0].pageX;
            },
            handleTouchEnd = e => {
                touchEndX = e.changedTouches[0].pageX;
                handleSwipe();
            };
        return {//連想配列（オブジェクト）
            init: () => {//初期化メソッドの定義
                //ループ用ダミーアイテム追加
                appendItem();

                $('.js-slider--next').on('click', function () {
                    if (!$sliderList.is(':animated')) slideNext();
                    clearInterval(intervalId);
                    autoSlide();
                });

                $('.js-slider--prev').on('click', function () {
                    if (!$sliderList.is(':animated')) slidePrev();
                    clearInterval(intervalId);
                    autoSlide();
                });

                // タブが非アクティブになった時のイベントハンドラを追加
                $(document).on("visibilitychange", function () {
                    if (document.visibilityState === "hidden") {
                        clearInterval(intervalId);
                    } else {
                        clearInterval(intervalId);
                        autoSlide();
                    }
                });
                //スワイプイベントの追加
                $sliderList.on('touchstart', handleTouchStart);
                $sliderList.on('touchend', handleTouchEnd);

                //リサイズ処理
                if ($(location).attr('pathname') === str) {
                    if (screenWidth > screenHeight) {
                        // 横表示の処理
                        $(window).on('resize', function () {
                            let screenWidth = $(window).width(),
                                screenHeight = $(window).height();
                            if (screenWidth < screenHeight) {
                                // 縦表示の処理
                                location.reload();
                            }
                        });
                    } else {
                        // 縦表示の処理
                        $(window).on('resize', function () {
                            let screenWidth = $(window).width(),
                                screenHeight = $(window).height();
                            if (screenWidth > screenHeight) {
                                // 横表示の処理
                                location.reload();
                            }
                        });
                    }
                }
                //自動スライド
                autoSlide();
            },
        }
    })();
    Slider.init();

    /*================================================================
    画像切り替え
    ================================================================*/
    const ChangeImg = (() => {
        let $switchImgMain = $('.js-switch-img-main'),
            $switchImgSubs = $('.js-switch-img-sub');
        return {
            init: () => {
                $switchImgSubs.on('click', function () {
                    let $this = $(this),
                        srcBox = $switchImgMain.attr('src');
                    $switchImgMain.attr('src', $this.attr('src'));
                    $this.attr('src', srcBox);
                });
            }
        }
    })();
    ChangeImg.init();

    /*================================================================
    画像ライブプレビュー
    ================================================================*/
    const LivePreview = (() => {
        let $dropArea = $('.js-areaDrop'),
            $fileInput = $('.js-inputFile'),
            $removeBtn = $('.js-removeImg');

        return {
            init: () => {
                //ドラッグアンドドロップイベントの登録
                $dropArea.on('dragover', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    $(this).css('opacity', .5);
                });
                $dropArea.on('dragleave', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    $(this).css('opacity', 1);
                });
                $dropArea.on('drop', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    let $this = $(this),
                        $fileInput = $this.find('.js-inputFile');
                    $this.css('opacity', 1);
                    //<input type="file">にはFilesListオブジェクトにデータが格納されている
                    //dataTransferにはFilesListオブジェクトが含まれている
                    //FilesListオブジェクトごと渡せば、<input type="file">にデータを渡せる
                    //jQueryオブジェクトにはdataTransferはないため、originalEventを使う
                    $fileInput[0].files = e.originalEvent.dataTransfer.files;
                    //<label>要素を使用してファイルをドロップした場合、changeイベントが発生品ため、
                    //ドロップイベントの時は手動トリガーで発火
                    $fileInput.trigger("change");
                });
                $fileInput.on('change', function () {
                    //ドロップイベントの時は手動トリガー、
                    //clickでファイルを選択した場合は普通にchangeイベントが発火
                    $dropArea.css('opacity', 1);
                    let file = this.files[0], // 2. files配列にファイルが入っている
                        $wrap = $(this).closest('.js-showDropTarget'),
                        $img = $(this).siblings('.js-prevTarget'), // 3. jQueryのsiblingsメソッドで兄弟のimgを取得
                        fileReader = new FileReader();   // 4. ファイルを読み込むFileReaderオブジェクト
                    //5. 読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
                    fileReader.onload = function (event) {
                        // 読み込んだデータをimgに設定
                        $img.attr('src', event.target.result).show();
                    };
                    // 6. 画像読み込み
                    fileReader.readAsDataURL(file);
                    // 画像が読み込まれた後の処理
                    $wrap.next('.js-showDropTarget').show();
                    $wrap.find('.js-removeImg').show();
                });

                //作品編集用DB保存の作品画像表示
                $.each($('.js-prevTarget'), function () {
                    let $this = $(this);
                    if ($this.attr('src').length) {
                        let $wrap = $this.closest('.js-showDropTarget');
                        $wrap.show().next('.js-showDropTarget').show();
                        $wrap.find('.js-removeImg').show();
                    }
                });

                //画像削除
                $removeBtn.on('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    let $that = $(this),
                        $parent = $that.closest('.js-showDropTarget'),
                        $img = $that.siblings('.js-areaDrop').find('.js-prevTarget'),
                        $file = $that.siblings('.js-areaDrop').find('.js-inputFile'),
                        $nextTargets = $parent.nextAll('.js-showDropTarget');
                    // 削除した要素が最後の要素でない場合、削除した要素以降の要素を一つずつ前に詰める
                    if ($nextTargets.length > 0) {
                        $.each($nextTargets, function (index) {
                            let $next = $(this),
                                $nextImg = $next.find('.js-prevTarget'),
                                $nextFile = $next.find('.js-inputFile'),
                                $nextRemove = $next.find('.js-removeImg'),
                                $currentImg = $next.prev('.js-showDropTarget').find('.js-prevTarget'),
                                $currentFile = $next.prev('.js-showDropTarget').find('.js-inputFile'),
                                $currentRemove = $next.prev('.js-showDropTarget').find('.js-removeImg');
                            /*==============
                            画像処理
                            ==============*/
                            //処理をしている次の要素に画像が存在する場合、次の要素の画像を現在の要素に詰め、次の要素の画像は消す
                            if ($nextImg.attr('src').length) {
                                $currentImg.attr('src', $nextImg.attr('src'));
                                $nextImg.attr('src', '');
                                if (index === $nextTargets.length - 1) {
                                    $nextImg.hide();
                                    $nextRemove.hide();
                                }
                                //処理をしている次の要素に画像が存在しない場合、現在の画像・削除ボタン、次の要素自体を隠す
                            } else {
                                $currentImg.attr('src', '').hide();
                                $currentRemove.hide();
                                $next.hide();
                            }
                            /*==============
                            ファイル処理
                            ==============*/
                            if ($nextFile[0].files.length) {
                                //DataTransferでfile情報を詰め直す
                                let dt = new DataTransfer(),
                                    file = $nextFile[0].files;
                                dt.items.add(file[0]);
                                $currentFile[0].files = dt.files;
                                //changeイベントでファイルを読み込む
                                $currentFile.trigger("change");
                                $nextFile.val('');
                            } else {
                                $currentFile.val('');
                            }
                            /*==============
                            編集用削除時のPOST用処理
                            ==============*/
                            //input type=hiddenに詰め直して、POSTする
                            $.each($removeBtn, function (index, value) {
                                let $img = $('.js-prevTarget'),
                                    selector = "#hiddenPic" + (index + 1);
                                if (value === $currentRemove[0]) {
                                    $(selector).val($currentImg.attr('src'));
                                } else {
                                    $(selector).val($($img[index]).attr('src'));
                                }
                            });
                        });
                    } else {
                        // 削除した要素が最後の要素だった場合の処理
                        $img.attr('src', '').hide();
                        $file.val('');
                        $that.hide();

                        // $.each($removeBtn, function(index){
                        //     let selector = "#hiddenPic" + (index + 1),
                        //         $img = $('.js-prevTarget');
                        //     $(selector).val($($img[index]).attr('src'));
                        // });
                    }
                });
            }
        }
    })();
    LivePreview.init();

    /*================================================================
    GETパラメータ付与
    ================================================================*/
    const AppendGet = (() => {
        let appendAttr = function ($elem, name, value, key) {
            if (name === key) {
                $elem.attr({name: name, value: value});
            }
        }
        return {
            init: () => {
                $('.js-insertCategorySelect').on('click', function () {
                    let $that = $(this),
                        $selectTarget = $('.js-insertCategoryTarget');
                    $.each($selectTarget, function () {
                        let $this = $(this);
                        if ($this.val() === $that.val()) {
                            $this.prop('selected', true);
                        }
                    });
                    $('.js-onSearch').trigger("click");
                });

                $(".js-appendGet").on('click', function (e) {
                    e.preventDefault();
                    let $this = $(this),
                        $hiddenSort = $('#js-hiddenSort');
                    appendAttr($hiddenSort, $this.attr('name'), $this.attr('value'), 'sort');
                    $('.js-onSearch').trigger("click");
                });
            }
        }
    })();
    AppendGet.init();

    /*================================================================
    連絡掲示板メッセージ処理
    ================================================================*/
    const LiveMsg = (() => {
        //スクロール最下部
        let msgScroll = function () {
                let $scrollBottom = $('.js-scrollBottom');
                if ($scrollBottom.length) {
                    $scrollBottom.animate({scrollTop: $scrollBottom[0].scrollHeight}, 'fast');
                }
            },
            msgContainerScroll = function () {
                let $scrollBottom = $('.js-scrollBottom'),
                    position = $scrollBottom.offset().top;
                $("html, body").animate({scrollTop: position}, 'fast');
            },
            hideNoMsg = function () {
                let $hideItem = $('.js-hide');
                if ($hideItem) $hideItem.hide();
            },
            templateMsg = function (data) {
                let $clone = $($('#msgRight').html());
                const $msg = $clone.find('.js-insertMsg');
                const $date = $clone.find('.js-insertDate');
                const $img = $clone.find('.js-insertImg');
                $msg.text(data.send_msg);
                $date.text(data.date);
                if (data.pic !== null) {
                    $img.attr('src', data.pic);
                } else {
                    $img.attr('src', '../src/img/noImage.png');
                }
                $($clone.prop('outerHTML')).appendTo($('.js-appendMsg'));
                msgScroll();
                hideNoMsg();
                msgContainerScroll();
            },
            templateDbMsg = function (data) {
                $.each(data, function (index, value) {
                    let $clone = $($('#msgLeft').html());
                    const $msg = $clone.find('.js-insertMsg');
                    const $date = $clone.find('.js-insertDate');
                    const $img = $clone.find('.js-insertImg');
                    $msg.text(value.msg);
                    $date.text(value.update_date);
                    if (value.pic !== null) {
                        $img.attr('src', value.pic);
                    } else {
                        $img.attr('src', '../src/img/noImage.png');
                    }
                    $($clone.prop('outerHTML')).appendTo($('.js-appendMsg'));
                });
                msgScroll();
                hideNoMsg();
            }
        return {
            init: () => {
                $('.js-submitForm').on('submit', function (e) {
                    e.preventDefault();

                    // 操作対象のフォーム要素を取得
                    let $form = $(this),
                        // 送信ボタンを取得（後で使う: 二重送信を防止する。）
                        $button = $form.find('input[type="submit"]'),
                        $textarea = $form.find('textarea'),
                        p_id = $(location).attr('search').replace(/[^0-9]/g, "");
                    $.ajax({
                        type: 'post',
                        url: 'ajaxMsg.php',//送信先のパス。パスはこのjsファイルから見た時のどこか。「相対パス」
                        dataType: "json",
                        data: {//サーバーに送るデータ
                            send_msg: $form.serializeArray(),
                            p_id: p_id
                        },
                        beforeSend: function () {
                            // 通信を行う前処理
                            // ボタンを無効化し、二重送信を防止
                            $button.attr('disabled', true);
                        },
                        complete: function () {
                            // 通信が成功してもエラーになっても実行される場所
                            console.log("ajax complete");
                            // ボタンを有効化し、再送信を許可
                            $button.attr('disabled', false);
                            $textarea.val("");
                        }
                    }).done(function (data, status) {//.done：Ajax通信が終わったら実行　第一引数：サーバーから帰ってきた値、第二引数：通信が成功したかの文字列"success"
                        console.log(data);
                        console.log(status);
                        if (data) {
                            templateMsg(data);
                        }
                    }).fail(function (msg) {
                        console.log('Ajax Error：', msg);
                    });
                });

                msgScroll();
            },
            monitorDatabase: () => {
                // データベースの監視処理を実行する間隔（ミリ秒）
                const INTERVAL = 30000;

                let p_id = $(location).attr('search').replace(/[^0-9]/g, "");
                setInterval(function () {
                    // データベースの監視処理を実行するAJAXリクエスト
                    $.ajax({
                        url: 'ajaxCheckDatabase.php', // データベースをチェックするPHPスクリプトのパス
                        type: 'post',
                        dataType: 'json',
                        data: {p_id: p_id}
                    }).done(function (data, status) {//.done：Ajax通信が終わったら実行　第一引数：サーバーから帰ってきた値、第二引数：通信が成功したかの文字列"success"
                        console.log(data);
                        console.log(status);
                        if (data) {
                            // 新しいレコードが挿入された場合、画面を更新する処理を実行
                            templateDbMsg(data);
                        }
                    }).fail(function (msg) {
                        console.log('Ajax Error：', msg);
                    });
                }, INTERVAL);
            }
        }
    })();

    if ($(location).attr('pathname').split('/').pop() === "msg.php") {
        console.log('auto update');
        LiveMsg.init();
        //監視を開始
        LiveMsg.monitorDatabase();
    }

    /*================================================================
    マイページサイドバーアクティブ
    ================================================================*/
    const ActiveSidebar = (() => {
        let $sidebar = $(".js-addBold");
        return {
            init: () => {
                if ($sidebar.length) {
                    $.each($sidebar, function () {
                        let $this = $(this);
                        if ($(location).attr('pathname').split('/').pop() === $this.attr('href').split('/').pop()) {
                            $this.addClass("js-addBold--active");
                        }
                    });
                }
            }
        }
    })();
    ActiveSidebar.init();

    /*================================================================
    画面切り替え
    ================================================================*/
    const ChangeContents = (() => {
        /*==================================
        property
        ==================================*/
        let $contentsChangeBtn = $('.js-changeContents'),
            $leftTarget = $("#js-headLeftTarget"),
            $rightTarget = $("#js-headRightTarget"),
            $wrapTarget = $("#js-wrapTarget"),
            s_id = $(location).attr('hash').slice(1) || null,
            p_id = null,
            title = '',
            sort = 0,
            currentPageNum = 1;
        /*==================================
        method
        ==================================*/
        //初期セット
        let initTitle = () => {
                $.each($contentsChangeBtn, function () {
                    let $this = $(this);
                    if ($this.attr('href').substring($this.attr('href').indexOf("#") + 1) === s_id) {
                        title = $this.text();
                        $this.addClass('js-changeContents--active');
                    }
                });
            },
            showContents = (temp, data, s_id) => {
                //content
                let contents = '',
                    contentTemplate = '';
                //作品表示（sortがある場合はソートされた作品表示）
                switch (s_id) {
                    case 'favoriteProduct':
                    case 'browsingProduct':
                        contentTemplate = $(temp).filter("#favoriteTemplate").html();
                        break;
                    case 'board':
                        contentTemplate = $(temp).filter("#boardTemplate").html();
                        break;
                    case 'draftProduct':
                        contentTemplate = $(temp).filter("#draftsTemplate").html();
                        break;
                    case 'saleProduct':
                        contentTemplate = $(temp).filter("#salesTemplate").html();
                        break;
                }
                // テンプレートをコンパイル
                let compiledContent = _.template(contentTemplate);
                $.each(data, function () {
                    console.log(this);
                    this.s_id = s_id;
                    // データをバインドしてHTMLを生成
                    contents += compiledContent(this);
                })

                // 生成されたコンテンツをページに追加
                return contents;
            },
            showList = (temp, data) => {
                let list = '';
                let listTemplate = $(temp).filter("#sortListTemplate").html();
                // テンプレートをコンパイル
                let compiledList = _.template(listTemplate);
                $.each(data, function (index, value) {
                    // データをバインドしてHTMLを生成
                    list += compiledList({index: index, value: value});
                });
                return list;
            },
            contentChange = () => {
                $.ajax({
                    type: "POST",
                    url: "ajaxTemplateData.php",
                    dataType: "json",
                    data: {
                        s_id: s_id,
                        currentPageNum: currentPageNum,
                        sort: sort,
                        p_id: p_id
                    }
                }).done(function (data, status) {
                    console.log(data);
                    console.log(status);
                    if (data) {
                        // テンプレートを外部ファイルから読み込む
                        $.get("template.php", function (temp) {
                            //ソートがない場合（サイドバークリック時）
                            //タイトル・作品件数表示
                            let headLeftData = {
                                title: title,
                                currentMinNum: data.currentMinNum,
                                currentMaxNum: data.currentMinNum + data.dbData.length,
                                total: data.total
                            }
                            //headLeft
                            let leftTemplate = $(temp).filter("#headLeftTemplate").html();
                            // テンプレートをコンパイル
                            let compiledLeft = _.template(leftTemplate);
                            // データをバインドしてHTMLを生成
                            let leftHtml = compiledLeft(headLeftData);
                            // 生成されたコンテンツをページに追加
                            $leftTarget.html(leftHtml);

                            //ドロップダウンリスト表示
                            let headRightData = {
                                list: showList(temp, data.sortData),
                                currentItem: data.sortData[sort]
                            }
                            //headRight
                            let rightTemplate = $(temp).filter("#headRightTemplate").html();
                            // テンプレートをコンパイル
                            let compiledRight = _.template(rightTemplate);
                            // データをバインドしてHTMLを生成
                            let rightHtml = compiledRight(headRightData);
                            // 生成されたコンテンツをページに追加
                            $rightTarget.html(rightHtml);

                            //作品がある場合
                            if (data.dbData) {
                                console.log('作品データあり');
                                let wrapData = {
                                    s_id: s_id,
                                    contents: showContents(temp, data.dbData, s_id),
                                    pagination: data.pagination,
                                    total: data.total
                                }
                                console.log('wrapData:', wrapData);
                                let wrapTemplate = '';
                                switch (s_id) {
                                    case 'favoriteProduct':
                                    case 'browsingProduct':
                                    case 'saleProduct':
                                        wrapTemplate = $(temp).filter("#contentWrapTemplate").html();
                                        break;
                                    case 'board':
                                    case 'draftProduct':
                                        wrapTemplate = $(temp).filter("#tableTemplate").html();
                                        break;
                                }
                                // テンプレートをコンパイル
                                let compiledWrap = _.template(wrapTemplate);
                                // データをバインドしてHTMLを生成
                                let wrapHtml = compiledWrap(wrapData);
                                // 生成されたコンテンツをページに追加
                                $wrapTarget.html(wrapHtml);

                                //作品がない場合
                            } else {
                                let noContentTemplate = $(temp).filter("#noContentTemplate").html();
                                // テンプレートをコンパイル
                                let compiledNoContent = _.template(noContentTemplate);
                                // データをバインドしてHTMLを生成
                                let noContentHtml = compiledNoContent({title: title});
                                // 生成されたコンテンツをページに追加
                                $wrapTarget.html(noContentHtml);
                            }
                        });
                    } else {
                        console.log('データなし');
                    }
                }).fail(function (msg) {
                    console.log('Ajax Error：', msg);
                });
            }
        return {
            selectContents: () => {
                $contentsChangeBtn.on('click', function () {
                    console.log('click');
                    let $this = $(this);
                    title = $this.text();
                    sort = $this.data('sort') || 0;
                    currentPageNum = $this.data('currentpagenum') || 1;
                    s_id = $this.attr('href').substring($this.attr('href').indexOf("#") + 1);
                    p_id = $this.data('p_id') || null;

                    $this.addClass('js-changeContents--active');
                    $contentsChangeBtn.not($this).removeClass('js-changeContents--active');
                    contentChange();
                });
            },
            sortContents: () => {
                $(document).on('click', ".js-dropDown__current", function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    let $that = $(this);
                    let over_flg = true;
                    $(".js-dropDown__target:not(:animated)").slideToggle();
                    $(".js-dropDown__current").hover(function () {
                        over_flg = true;
                    }, function () {
                        over_flg = false;
                    });
                    $(".js-dropDown__item").on('click', function () {
                        let $this = $(this),
                            $btn = $this.children("button"),
                            $activeList = $('.js-changeContents--active');
                        $that.text($btn.text());
                        $activeList.data('sort', $btn.val()).click();
                    })
                    $("body").on('click', function () {
                        if (over_flg === false) $(".js-dropDown__target").slideUp();
                    });
                });
            },
            pagingContents: () => {
                $(document).on('click', ".js-pagination", function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    let $this = $(this),
                        $activeList = $('.js-changeContents--active'),
                        $targetPos = $('.js-subTitlePos');
                    $activeList.data('currentpagenum', $this.val()).click();
                    $("body , html").scrollTop($targetPos.offset().top - 50);
                });
            },
            draftEdit: () => {
                $(document).on('click', ".js-draftEdit", function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    let $this = $(this),
                        sendData = {};
                    if ($this.attr('name') === 'delete_p_id') {
                        if (!confirm('削除しますか？')) return false;
                    }
                    sendData[$this.attr('name')] = $this.attr('value');

                    $.ajax({
                        url: 'ajaxDraftEdit.php', // データベースをチェックするPHPスクリプトのパス
                        type: 'post',
                        data: sendData,
                    }).done(function (data, status) {//.done：Ajax通信が終わったら実行　第一引数：サーバーから帰ってきた値、第二引数：通信が成功したかの文字列"success"
                        console.log(data);
                        console.log(status);
                        $.each($contentsChangeBtn, function () {
                            let $this = $(this);
                            if ($this.attr('href').substring($this.attr('href').indexOf("#") + 1) === 'draftProduct') $this.click();
                        });
                    }).fail(function (msg) {
                        console.log('Ajax Error：', msg);
                    });
                });
            },
            favoriteEdit: () => {
                // お気に入り登録・削除
                $(document).on('click', ".js-favorite", function () {
                    let $this = $(this),
                        p_id = $this.data('pid') || null;
                    $.ajax({
                        type: "POST",
                        url: "../common/ajaxFavorite.php",
                        data: {p_id: p_id}
                    }).done(function (data, status) {
                        console.log(data);
                        console.log(status);
                        // クラス属性をtoggleでつけ外しする
                        if (data) {
                            if ($(location).attr('pathname').split('/').pop() === "sales_contents.php") {
                                $.each($contentsChangeBtn, function () {
                                    let $this = $(this);
                                    if ($this.attr('href').substring($this.attr('href').indexOf("#")) === $(location).attr('hash')) $this.click();
                                });
                            } else {
                                $this.toggleClass('js-favorite--active')
                                    .children('.fa-heart').toggleClass('fa-solid').toggleClass('fa-regular');
                                $this.children('.js-favoriteCount').text(data);
                            }
                        } else {
                            if (confirm('ログインしますか？')) {
                                $(location).attr('href', '../auth/login.php');
                            }
                        }
                    }).fail(function (msg) {
                        console.log('Ajax Error：', msg);
                    });
                });
            },
            init: () => {
                initTitle();
                contentChange();
            }
        }
    })();

    if ($(location).attr('pathname').split('/').pop() === "sales_contents.php") {
        ChangeContents.init();
        ChangeContents.selectContents();
        ChangeContents.pagingContents();
        ChangeContents.draftEdit();
    }
    //作品一覧ページ兼用
    ChangeContents.sortContents();
    //作品詳細ページ兼用
    ChangeContents.favoriteEdit();
});