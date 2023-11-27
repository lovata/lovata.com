export default new class Common {
    constructor() {
        this.runCommon();
    }

    runCommon () {
        var conf = {
            smoothScrollSpeed: 1400,
            smoothScrollOffset: 100,
            classHeaderBar: '_bar',
            owlCarouselInitWidth: 960,
            headerBtnOffsetHideHeight: 250,
            mobileWidth: 540,
            tabletWidth: 1048,
            technologySmoothScrollOffset: 50,
            popupOpenClass: '_open',
            bodyFixedClass: '_fixed',
            wrapperClass: '_padding-bottom',
            slideTogglePluginsSpeed: 600
        };

        var $header = $('.header'),
            $aside = $('.aside'),
            $headerBtnRequest = $('.header__request'),
            bottomPageVal = $(document).height() - $(window).height() - conf.headerBtnOffsetHideHeight,
            $popup = $('.popup-wrap'),
            $body = $('body'),
            offsetTop = 0;

        eventsHandler();
        setLogoColor();
        isRequestPage();
        isBlogPostPage();
        toggleArrowDown();

        switch(getPageId()) {
            case 'october-cms':
                addClutchWidget();
                setOctoberGitHubStars();
                break;
        }

        function isIphone () {
            var isIphone = /iPhone/.test(navigator.userAgent) && !window.MSStream;

            return isIphone;
        }

        function getPageId () {
            var $page = $('[data-page-id]'),
                pageID;

            if (!$page.length) return;

            pageID = $page.data('page-id');

            !pageID.length ? pageID : null;

            return pageID;
        }

        function addClutchWidget () {
            var companyId = '70674',
                $widgetContainer = $('.clutch-widget__container');

            if (isIphone()) {
                $widgetContainer.append('<div style="max-width: 280px" class="clutch-widget _iphone" data-url="https://clutch.co" data-widget-type="3" data-height="400px" data-snippets="true" data-clutchcompany-id="'+companyId+'"></div>');
            } else {
                $widgetContainer.append('<div style="max-width: 960px" class="clutch-widget" data-url="https://clutch.co" data-widget-type="4" data-height="860px" data-snippets="true" data-clutchcompany-id="'+companyId+'"></div>');
            }
        }

        setTimeout( function () {
            smoothScrollOnLoad();
        },1000);

        function eventsHandler() {
            var $doc = $(document),
                $win = $(window);

            if ($('.owl-carousel').length) {
                $win.on('resize load', function () {
                    controlOwlCarousel();
                });
            }

            if ($('.october-cms').length) {
                $win.on('resize', function () {
                    if($(window).width() > 640) {
                        $('.cms--plugins-item').each( function () {
                            if(!$(this).hasClass('_mobile-view')) {
                                $(this).removeClass('_hide');
                            }
                        });
                    }
                });
            }

            $doc.on('click','.js-btnTogglePluginsView', function () {
                togglePluginsView($(this))
            });

            $doc.on('click','.js-btnToggleTagsView', function () {
                toggleTagsView($(this))
            });

            $doc.on('click','.popup-wrap', function (e) {
                closePopUp(e)
            });

            $doc.on('click', '.js-uploadImg', function () {
                openPopUp();
                uploadImg($(this));
            });

            $doc.on('click', '.portfolio-tmpl__tech-link', function (e) {
                if ($(this).hasClass('stub')) e.preventDefault();
            });

            $doc.on('click', '[data-btn-request]', function () {
                setUrlParameter($(this));
            });

            $doc.on('click', '.aside__nav-link', function () {
                if ($(window).width() < conf.tabletWidth) {
                    toggleAside();
                }
            });

            $doc.on('scroll', function () {
                toggleHeaderBar();
                hideHeaderBtn();
            });

            $doc.on('click', '.header__navicon, .aside__btn-close', function (e) {
                e.preventDefault();
                toggleAside();
            });

            $doc.on('mouseenter', '.wrp', function (e) {
                if ($(e.relatedTarget).closest($aside).length && $(window).width() > conf.tabletWidth) {
                    hideAside();
                }
            });

            $doc.on('click', '.aside__nav-link, .header__nav-logo-link , .main__portfolio-nav-link, .aside__logo-link', function (e) {
                detectPageForLinkAction(e, this);
            });

            $doc.on('click', '.main__build-link', function (e) {
                if($(this).hasClass('_stub')) e.preventDefault();
            });

            $doc.on('click', '._portfolio-preview-stub', function (e) {
                e.preventDefault();
            });

            $doc.on('click', '[data-tab]', function (e) {
                toggleContentTabs(this);
                detectPageForLinkAction(e, this)
            });

            $doc.on('click', '[data-tab-id]', function () {
                togglePortfolioSlide(this);
            });
        }

        function setOctoberGitHubStars() {
            $.get('https://api.github.com/repos/octobercms/october').then(function(res) {
                $('.cms--title-git-text').text(res.stargazers_count);
            });
        }

        function togglePluginsView($btn) {

            $('.plugins--item').each( function () {
                if(!$(this).hasClass('_mobile-view')) {
                    $(this).toggleClass('_hide');
                }
            });

            if($btn.hasClass('_open-list')) {
                $btn.text('Show all plugins');
                $btn.removeClass('_open-list')
            } else {
                $btn.text('Hide full list');
                $btn.addClass('_open-list')
            }
        }

        function toggleTagsView($btn) {

            $('.blog--tags-item').each( function () {
                if(!$(this).hasClass('_mobile-view')) {
                    $(this).toggleClass('_show');
                }
            });
        }

        function openPopUp() {
            offsetTop = $(document).scrollTop();

            $header.hide();
            $popup.addClass(conf.popupOpenClass);
            $body.addClass(conf.bodyFixedClass);

            $body.css({top: -offsetTop});
        }

        function closePopUp(e) {
            if($(e.target).hasClass('portfolio-tmpl__popup-img')) return false;

            $header.show();
            $body.removeClass(conf.bodyFixedClass);
            $body.scrollTop(offsetTop);
            $popup.removeClass(conf.popupOpenClass);
            $popup.find('.popup-wrap__inner').remove();
        }

        function uploadImg($img) {
            var imgPath = $img.data('img-path'),
                img = '<div class="popup-wrap__inner">' +
                    '<div class="portfolio-tmpl__popup-img-wrap">' +
                    '<img class="portfolio-tmpl__popup-img" src="' + imgPath +'">' +
                    '</div>' +
                    '</div>';

            $popup.append(img);
        }

        function hideHeaderBtn() {
            if ($(window).width() > conf.mobileWidth && !$('.js-requestPage').length) {
                $header.offset().top >= bottomPageVal ? $headerBtnRequest.fadeOut() : $headerBtnRequest.fadeIn();
            }
        }

        function setUrlParameter(link) {
            var hash = window.location.href,
                linkHref = link.attr('href');

            link.attr('href', linkHref + '?' + hash);
        }

        function isRequestPage() {
            var hash = window.location.search.replace(/\?/g, '');

            if ($('.js-requestPage').length) {
                $('.header__request').addClass('_hide');
                $('.header__back').show().attr('href', hash);
            }
        }

        function isBlogPostPage() {
            if ($('.page-post').length && $(window).width() < 1024) {
                $('.wrp').addClass(conf.wrapperClass)
            }
        }

        function controlOwlCarousel() {
            var $owl = $('.owl-carousel');

            if ($(window).width() < conf.owlCarouselInitWidth) {
                $owl.owlCarousel({autoWidth: true});
                return;
            }

            $owl.trigger('destroy.owl.carousel');
        }

        function toggleHeaderBar() {
            switch ($header.offset().top > 50 && !$('._aside-open').length) {
                case (true):
                    $header.addClass(conf.classHeaderBar);
                    break;
                case (false):
                    $header.removeClass(conf.classHeaderBar);
                    break;
            }
        }

        function toggleContentTabs(el) {

            $(el).closest('.js-tabs').find('[data-tab]').removeClass('_active');

            var tabId = $(el).data('tab'),
                $tabContent = $('[data-tab-content]');

            $tabContent.each(function () {
                var $contentItem = $(this);
                if ($contentItem.data('tab-content') == tabId) {
                    $('[data-tab]').each(function () {
                        if ($(this).data('tab') == tabId) {
                            $(this).addClass('_active');
                        }
                    });
                    $contentItem.closest('.js-tabsContent').find('[data-tab-content]').removeClass('_active');
                    $contentItem.addClass('_active');
                }
            });
        }

        function togglePortfolioSlide(el) {
            var $tabs = $(el).closest('.js-tabs'),
                $tabsContent = $(el).closest('.js-tabsContent'),
                tabId = $(el).data('tab-id'),
                $images = $tabsContent.find('[data-tab-img]');

            $tabs.find('[data-tab-id]').removeClass('_active');

            $images.each(function () {
                var image = $(this);
                if (image.data('tab-img') == tabId) {
                    $(el).addClass('_active');
                    image.closest('.js-tabsContent').find('[data-tab-img]').removeClass('_active');
                    image.addClass('_active');
                }
            });
        }

        function toggleAside() {
            $('.main-wrap').toggleClass('_aside-open');
            $aside.toggleClass('_open');
            $header.toggleClass('_open');
            $('.aside__btn-close').toggleClass('_open');
            $('.header__navicon').toggleClass('_open');
            $('.header__nav-logo-link').toggleClass('_hide');

            if ($header.hasClass(conf.classHeaderBar)) {
                $header.removeClass(conf.classHeaderBar);
            } else if ($header.offset().top > 50) {
                $header.addClass(conf.classHeaderBar);
            }
        }

        function hideAside() {
            $('.main-wrap').removeClass('_aside-open');
            $aside.removeClass('_open');
            $header.removeClass('_open');
            $('.aside__btn-close').removeClass('_open');
            $('.header__navicon').removeClass('_open');
            $('.header__nav-logo-link').removeClass('_hide');

            if ($header.hasClass(conf.classHeaderBar)) {
                $header.removeClass(conf.classHeaderBar);
            } else if ($header.offset().top > 50) {
                $header.addClass(conf.classHeaderBar);
            }
        }

        function setLogoColor() {
            var $logoColor = $('[data-logo]');

            switch ($logoColor.data('logo')) {
                case 'black':
                    return $header.addClass('_black-logo');
                case 'burger-white':
                    return $header.addClass('_burger-white');
            }
        }

        function detectPageForLinkAction(e, link) {
            var hrefLink = $(link).data('href');

            if ($(link).hasClass('index') && $('.index-page').length) {
                smoothScroll(e, link);
            } else if ($(link).hasClass('portfolio-smooth-scroll')) {
                smoothScroll(e, link);
            } else {
                $(link).attr('href', hrefLink);
            }
        }

        function smoothScrollOnLoad() {
            var hash = window.location.hash,
                item = $(hash);

            if (!hash) return;

            $('html, body').stop().animate({
                scrollTop: item.offset().top - conf.smoothScrollOffset
            }, conf.smoothScrollSpeed);
        }

        function smoothScroll(e, link) {

            if ($('.cpt').length) {
                conf.smoothScrollOffset = 350;
            }

            e.preventDefault();
            var hash = $(link).attr('href'),
                item = $(hash);
            $('html, body').stop().animate({
                scrollTop: item.offset().top - conf.smoothScrollOffset
            }, conf.smoothScrollSpeed);
        }

        function toggleArrowDown() {

            if(!$('.october-cms').length) return false;

            var $cmsArrow = $('.cms--arrow'),
                $cmsContent = $('#cms-content');

            setTimeout(function () {
                $cmsArrow.addClass('_show')
            },1000);

            //Events
            $(document)
                .on('click','.cms--arrow', function () {
                    $('html,body').animate({
                        scrollTop: $cmsContent.offset().top - conf.technologySmoothScrollOffset
                    }, 1200)
                })
                .on('scroll', function () {
                    if($header.offset().top > 250) {
                        $cmsArrow.removeClass('_show')
                    } else if($header.offset().top < 250) {
                        $cmsArrow.addClass('_show')
                    }})
        }
    }
};
