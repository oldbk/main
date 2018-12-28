jQuery(document).ready(function ($) {
    Shadowbox.init({
        language: 'ru',
        players: ['img']
    });

    //old slideshow
    $('.slidshow img:gt(0)').hide();//скрываем все картинки кроме 1го
    setInterval(function () {
        $('.slidshow :first-child').hide()//анимированно скрываем 1ую картинку
            .next('img').show()//и показываем вторую
            .end().appendTo('.slidshow');
    }, 4000);//повторяем это каждые 2000 мс

    //new slideshow
    var slides = document.querySelectorAll('#simple_slides .simple_slide');
    var currentSlide = 0;
    var slideInterval = setInterval(nextSlide, 4000);

    function nextSlide(){
        slides[currentSlide].className = 'simple_slide';
        currentSlide = (currentSlide+1)%slides.length;
        slides[currentSlide].className = 'simple_slide showing';
    }

    ShowQAMsg();


    var hwSlideSpeed = 700;
    var hwTimeOut = 10000;

    $('.slide').css({
        "position": "absolute",
        "top": '0', "left": '0'
    }).hide().eq(0).show();

    var slideNum = 0;
    var slideTime;
    slideCount = $("#slider .slide").length;
    var animSlide = function (arrow) {
        clearTimeout(slideTime);
        $('.slide').eq(slideNum).fadeOut(hwSlideSpeed);
        $('.slide').eq(slideNum).hide();
        if (arrow == "next") {
            if (slideNum == (slideCount - 1)) {
                slideNum = 0;
            }
            else {
                slideNum++
            }
        }
        else if (arrow == "prew") {
            if (slideNum == 0) {
                slideNum = slideCount - 1;
            }
            else {
                slideNum -= 1
            }
        }
        else {
            slideNum = arrow;
        }
        $('.slide').eq(slideNum).fadeIn(hwSlideSpeed, rotator);
        $(".control-slide.active").removeClass("active");
        $('.control-slide').eq(slideNum).addClass('active');
    }
    var $adderSpan = '';
    $('.slide').each(function (index) {
        $adderSpan += '<span class = "control-slide">' + index + '</span>';
    });
    $('<div class ="sli-links">' + $adderSpan + '</div>').appendTo('#slider-wrap');

    $(".control-slide:first").addClass("active");
    $('.control-slide').click(function () {
        var goToNum = parseFloat($(this).text());
        animSlide(goToNum);
    });
    var pause = false;
    var rotator = function () {
        if (!pause) {
            slideTime = setTimeout(function () {
                animSlide('next')
            }, hwTimeOut);
        }
    }
    $('#slider-wrap').hover(
        function () {
            clearTimeout(slideTime);
            pause = true;
        },
        function () {
            pause = false;
            rotator();
        });
    rotator();
});

function showBox(imagelink, titeletext) {
    Shadowbox.open({content: imagelink, player: 'img', title: titeletext, width: '920', height: '480'});
}

function sendmailpassw() {
    var loginP = document.getElementById("loginid").value;
    if (loginP == "" || loginP.length > 50) {
        alert("Введен некоректный login");
        return false;
    }
    else document.sendmailid.submit();
}