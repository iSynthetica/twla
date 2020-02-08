(function($) {
    $(document).foundation();

    $(document).ready(function() {
        initSmoothScroll();
        loadTestimonials();
        testimonialSlick();
        loadProducts();
        shopSlick();
        carouselGallery();
        initArticleContentLoad();
        init_scroll_up();

        $('select').select2({
            placeholder: 'Country *',
            allowClear: true,
            width: '100%'
        });

        $(document).on('click', '.gallery-link', function() {
            var id = $(this).attr('data-gallery');
            loadGallery(id);
        });
    });

    function initArticleContentLoad() {
        $(document).on('click', 'article.post-item a', function() {
            var currentLink = $(this),
                postId = currentLink.attr('data-id'),
                postArticle = currentLink.parent('article');

            var data = {
                action: 'load-post',
                id: postId
            };

            if (postArticle.is('.type-testimonial')) {
                data.postType = 'testimonial';
            } else if (postArticle.is('.type-post')) {
                data.postType = 'post';
            }

            handleAjaxRequest(data, function(response) {
                $('#postModal .post-content').html(response.html);
                $('#postModal').foundation('open');
            });

            return false;
        });
    }

    /**
     * Add smooth scroll to navigation menu
     */
    function initSmoothScroll() {
        $('a[href^="#"]').on('click',function (e) {
            e.preventDefault();

            var target = this.hash;
            var $target = $(target);

            $('html, body').stop().animate({
                'scrollTop': $target.offset().top
            }, 900, 'swing', function () {
                window.location.hash = target;
            });
        });
    }

    /**
     * Testimonials
     */
    function loadTestimonials() {
        var charLink = $('.chars-list .char');
        var topBarChar = $('.section-topbar .char');

        charLink.on('click', function() {
            var currentChar = $(this);
            var currentCharValue = currentChar.text();

            var data = {
                action: 'load-testimonials',
                char: currentCharValue
            };

            handleAjaxRequest(data, function(response) {
                makeCharActive(currentChar);
                topBarChar.text(currentCharValue);
                $('#testimonials .section-content').html(response.html);
                testimonialSlick();
            });
        });
    }

    function makeCharActive(char) {
        $('.chars-list .char').removeClass('active');
        $('.chars-list .char-divider').removeClass('active');
        char.addClass('active');
        char.next('.char-divider').addClass('active');
        char.prev('.char-divider').addClass('active');
    }

    function testimonialSlick() {
        $('.testimonials-carousel').slick({
            infinite: true,
            dots: false,
            arrows: false,
            slidesPerRow: 5,
            rows: 2,
            responsive: [
                {
                    breakpoint: 640,
                    settings: {
                        slidesPerRow: 2,
                        rows: 1
                    }
                },
                {
                    breakpoint: 1024,
                    settings: {
                        slidesPerRow: 3,
                        rows: 2
                    }
                }
            ]
        });

        $('.testimonials-topbar .prev-link .fa').click(function(e) {
            $('.testimonials-carousel').slick("slickPrev");
        });

        $('.testimonials-topbar .next-link .fa').click(function(e) {
            $('.testimonials-carousel').slick("slickNext");
        });
    }

    /**
     * Shop
     */
    function loadProducts() {
        var catTab = $('.shop-nav li');

        catTab.on('click', function() {
            var currentTab = $(this);
            var currentCat = currentTab.attr('data-category');

            var data = {
                action: 'load-products',
                category: currentCat
            };

            handleAjaxRequest(data, function(response) {
                catTab.removeClass('active');
                currentTab.addClass('active');
                $('#shop .section-content').html(response.html);
                shopSlick();
            });
        });
    }

    function shopSlick() {
        $('.shop-carousel').slick({
            dots: false,
            arrows: false,
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 3,
            responsive: [
                {
                    breakpoint: 640,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 2
                    }
                }
            ]
        });

        $('.shop-topbar .prev-link .fa').click(function(e) {
            $('.shop-carousel').slick("slickPrev");
        });

        $('.shop-topbar .next-link .fa').click(function(e) {
            $('.shop-carousel').slick("slickNext");
        });
    }

    /**
     * Handle ajax request and call cb function
     *
     * @param data
     * @param cbDone cb function if done
     * @param cbFail cb function if fail
     */
    function handleAjaxRequest(data, cbDone, cbFail) {
        $.post(SnthObj.ajaxurl, data, '' ,'json')
            .done(function(response) {
                if(response.type == 'success') {
                    cbDone(response);
                } else {
                    console.log(response.type);
                    console.log(response.id);
                }
            })
            .fail(function() {cbFail()})
    }

    function loadGallery(id) {
        var data = {
            action: 'load-gallery',
            id: id
        };

        handleAjaxRequest(data, function(response) {
            $('#contact .section-content').html(response.html);
            carouselGallery();
        }, function() {
            console.log('Failed');
        });
    }

    /**
     * Gallery
     */
    function carouselGallery() {
        $("#allinone_carousel_charming").allinone_carousel({
            skin: 'charming',
            showCircleTimer: 0,
            width: 990,
            height: 454,
            responsive:true,
            resizeImages:true,
            elementsHorizontalSpacing:120,
            autoHideNavArrows:false,
            showElementTitle:false,
            verticalAdjustment:50,
            showPreviewThumbs:false,
            numberOfVisibleItems:5,
            nextPrevMarginTop:23,
            playMovieMarginTop:0,
            showBottomNav:0
        });
    }

    /* -- Scroll Up -- */
    function init_scroll_up () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 250) {
                $('#scroll_up').css({bottom: "95px"});
            } else {
                $('#scroll_up').css({bottom: "-100px"});
            }
        });
        $('#scroll_up').click(function () {
            $('html, body').animate({scrollTop: '0px'}, 800);
            return false;
        });
    }

})(jQuery);