<script>
    <?php if(isset($closed)){?>
        alert("ユーザーが許可をキャンセルしました！");
        window.close();
    <?php } ?>

    function indexToggle(i) {
        $("#index" + i).toggle("fast");
    }

</script>
<style>
    ul, ol {
        margin-bottom: 0em;
    }

    h2 {
        margin: 0;
    }

    button {
        padding: 0.85em 1.5em 0.85em 1.5em;
    }

    @font-face {
        font-family: 'hooncat';
        src: url('/public/assets/fonts/HoonWhitecatR.ttf');
    }

    @font-face {
        font-family: 'mamelon';
        src: url('/public/assets/fonts/Mamelon.otf');
    }

    @font-face {
        font-family: 'TAKUMIYFONT';
        src: url('/public/assets/fonts/TAKUMIYFONT.ttf');
    }

    @font-face {
        font-family: 'TAKUMIYFONT_P';
        src: url('/public/assets/fonts/TAKUMIYFONT_P.ttf');
    }

    @font-face {
        font-family: 'TAKUMIYFONTMINI';
        src: url('/public/assets/fonts/TAKUMIYFONTMINI.ttf');
    }

    @font-face {
        font-family: 'TAKUMIYFONTMINI_P';
        src: url('/public/assets/fonts/TAKUMIYFONTMINI_P.ttf');
    }


    @media screen and (min-width: 737px) {

        .row > * {
            padding: 0 0 0 10em;
        }
    }

</style>


<div id="content" class="8u 12u(mobile) important(mobile)">
    <article class="box post">
        <?php
        if ($diaryInfo) { ?>
            <div id="canvas">
                <div id="book-zoom">
                    <div class="sj-book">
                        <div depth="5" class="hard">
                            <div class="side"></div>
                        </div>
                        <div depth="5" class="hard front-side">
                            <div class="depth"></div>
                        </div>
                        <div class="own-size"></div>
                        <div class="own-size even"></div>
                        <div class="hard fixed back-side p111">
                            <div class="depth"></div>
                        </div>
                        <div class="hard p112"></div>
                    </div>
                </div>
                <div id="slider-bar" class="turnjs-slider">
                    <div id="slider"></div>
                </div>
                <br/>
            </div>


            <script type="text/javascript">

                function loadApp() {

                    var flipbook = $('.sj-book');

                    // Check if the CSS was already loaded

                    if (flipbook.width() == 0 || flipbook.height() == 0) {
                        setTimeout(loadApp, 10);
                        return;
                    }

                    // Mousewheel

                    $('#book-zoom').mousewheel(function (event, delta, deltaX, deltaY) {


                        var data = $(this).data(),
                            step = 30,
                            flipbook = $('.sj-book'),
                            actualPos = $('#slider').slider('value') * step;

                        if (typeof(data.scrollX) === 'undefined') {
                            data.scrollX = actualPos;
                            data.scrollPage = flipbook.turn('page');
                        }

                        data.scrollX = Math.min($("#slider").slider('option', 'max') * step,
                            Math.max(0, data.scrollX + deltaX));

                        var actualView = Math.round(data.scrollX / step),
                            page = Math.min(flipbook.turn('pages'), Math.max(1, actualView * 2 - 2));

                        if ($.inArray(data.scrollPage, flipbook.turn('view', page)) == -1) {
                            data.scrollPage = page;
                            flipbook.turn('page', page);
                        }

                        if (data.scrollTimer)
                            clearInterval(data.scrollTimer);

                        data.scrollTimer = setTimeout(function () {
                            data.scrollX = undefined;
                            data.scrollPage = undefined;
                            data.scrollTimer = undefined;
                        }, 1000);

                    });

                    // Slider

                    $("#slider").slider({
                        min: 1,
                        max: 100,

                        start: function (event, ui) {

                            if (!window._thumbPreview) {
                                _thumbPreview = $('<div />', {'class': 'thumbnail'}).html('<div></div>');
                                setPreview(ui.value);
                                _thumbPreview.appendTo($(ui.handle));
                            } else
                                setPreview(ui.value);

                            moveBar(false);

                        },

                        slide: function (event, ui) {

                            setPreview(ui.value);

                        },

                        stop: function () {

                            if (window._thumbPreview)
                                _thumbPreview.removeClass('show');

                            $('.sj-book').turn('page', Math.max(1, $(this).slider('value') * 2 - 2));

                        }
                    });

                    // URIs

                    Hash.on('^page\/([0-9]*)$', {
                        yep: function (path, parts) {

                            var page = parts[1];

                            if (page !== undefined) {
                                if ($('.sj-book').turn('is'))
                                    $('.sj-book').turn('page', page);
                            }

                        },
                        nop: function (path) {

                            if ($('.sj-book').turn('is'))
                                $('.sj-book').turn('page', 1);
                        }
                    });

                    // Arrows

                    $(document).keydown(function (e) {

                        var previous = 37, next = 39;

                        switch (e.keyCode) {
                            case previous:

                                $('.sj-book').turn('previous');

                                break;
                            case next:

                                $('.sj-book').turn('next');

                                break;
                        }

                    });


                    // Flipbook

                    flipbook.bind(($.isTouch) ? 'touchend' : 'click', zoomHandle);

                    flipbook.turn({
                        elevation: 50,
                        acceleration: !isChrome(),
                        autoCenter: true,
                        gradients: true,
                        duration: 1000,
                        pages: 112,
                        when: {
                            turning: function (e, page, view) {

                                var book = $(this),
                                    currentPage = book.turn('page'),
                                    pages = book.turn('pages');

                                if (currentPage > 3 && currentPage < pages - 3) {

                                    if (page == 1) {
                                        book.turn('page', 2).turn('stop').turn('page', page);
                                        e.preventDefault();
                                        return;
                                    } else if (page == pages) {
                                        book.turn('page', pages - 1).turn('stop').turn('page', page);
                                        e.preventDefault();
                                        return;
                                    }
                                } else if (page > 3 && page < pages - 3) {
                                    if (currentPage == 1) {
                                        book.turn('page', 2).turn('stop').turn('page', page);
                                        e.preventDefault();
                                        return;
                                    } else if (currentPage == pages) {
                                        book.turn('page', pages - 1).turn('stop').turn('page', page);
                                        e.preventDefault();
                                        return;
                                    }
                                }

                                updateDepth(book, page);

                                if (page >= 2)
                                    $('.sj-book .p2').addClass('fixed');
                                else
                                    $('.sj-book .p2').removeClass('fixed');

                                if (page < book.turn('pages'))
                                    $('.sj-book .p111').addClass('fixed');
                                else
                                    $('.sj-book .p111').removeClass('fixed');

                                Hash.go('page/' + page).update();

                            },

                            turned: function (e, page, view) {

                                var book = $(this);

                                if (page == 2 || page == 3) {
                                    book.turn('peel', 'br');
                                }

                                updateDepth(book);

                                $('#slider').slider('value', getViewNumber(book, page));

                                book.turn('center');

                            },

                            start: function (e, pageObj) {

                                moveBar(true);

                            },

                            end: function (e, pageObj) {

                                var book = $(this);

                                updateDepth(book);

                                setTimeout(function () {

                                    $('#slider').slider('value', getViewNumber(book));

                                }, 1);

                                moveBar(false);

                            },

                            missing: function (e, pages) {

                                for (var i = 0; i < pages.length; i++) {
                                    addPage(pages[i], $(this));
                                }

                            }
                        }
                    });


                    $('#slider').slider('option', 'max', numberOfViews(flipbook));

                    flipbook.addClass('animated');

                    // Show canvas

                    $('#canvas').css({visibility: ''});
                }

                // Hide canvas

                $('#canvas').css({visibility: 'hidden'});

                // Load turn.js

                yepnope({
                    test: Modernizr.csstransforms,
                    yep: ['/public/assets/js/turnjs/turn.min.js'],
                    nope: ['/public/assets/js/turnjs/turn.html4.min.js', '/public/assets/css/turncss/jquery.ui.html4.css', '/public/assets/css/turncss/steve-jobs-html4.css'],
                    both: ['/public/assets/js/turnjs/steve-jobs.js', '/public/assets/css/turncss/jquery.ui.css', '/public/assets/css/turncss/steve-jobs.css'],
                    complete: loadApp
                });

            </script>

        <?php }else{ ?>
            <p>
                <button type="button" class="btn btn-danger" onclick="location.href='/Mypage/diary_insert_view'">
                    새요리일기등록
                </button>
            <h3>일기를 등록해 보세요~~!</h3>
        <?php } ?>
    </article>
</div>


