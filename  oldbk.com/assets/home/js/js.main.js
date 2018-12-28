$(document).ready(function () {
    var nameGr = ".kp-rang-cont-";
    var levelGr = ".kp-rang-sckull-";

    showwars = function (el) {
        $(el).toggleClass("im-24 im-24a");
        $(".war_list").toggleClass("close-wars open-wars");
    } // function show hide wars list
    showlevel = function () {
        $(".rang-t").each(function () {
            $(this).removeClass("show");
            $(this).addClass("hide");
        });
        $(".level-ct").toggleClass("hide show");
    }
    hidelevel = function () {
        $(".rang-t").each(function () {
            $(this).removeClass("show");
            $(this).addClass("hide");
        });
        if ($(".title-ct").hasClass("hide")) {
            $(".title-ct").toggleClass("hide show");
        }
    }
    defaultlevelplayers = function () {
        $(".level-gr").each(function () {
            $(this).removeClass("show");
            $(this).addClass("hide");
        });
        $(levelGr + "7").toggleClass("hide show");
    }
    showrang = function (el) {
        var titles = {
            "wins": "Победы",
            "skulls": "",
            "voink": "Воинственность",
            "wars": "Клановые войны",
            "wingl": "Великие битвы"
        };
        if (!$(el).hasClass("active")) {
            $(".kp-ico-rang").each(function () {
                $(this).removeClass("active");
            });
            $(el).addClass("active");
            $(".rang").each(function () {
                $(this).removeClass("show");
                $(this).addClass("hide")
            });
            var one = $(el).attr("data-el");
            $(nameGr + one).toggleClass("hide show");
            switch (one) {
                case "wins":
                    hidelevel();
                    $(".title-ct").html("<p>" + titles.wins + "</p>");
                    break;
                case "skulls":
                    showlevel();
                    break;
                case "voink":
                    hidelevel();
                    $(".title-ct").html("<p>" + titles.voink + "</p>");
                    break;
                case "wars":
                    hidelevel();
                    $(".title-ct").html("<p>" + titles.wars + "</p>");
                    break;
                case "wingl":
                    hidelevel();
                    $(".title-ct").html("<p>" + titles.wingl + "</p>");
                    break;
                default:
                    break;
            }
        }
    }
    showlevels_player = function (el) {
        $(".level-gr").each(function () {
            $(this).removeClass("show");
            $(this).addClass("hide");
        });
        $(levelGr + $(el).attr("data-el")).toggleClass("hide show");
    }
    showmobile = function (el) {
        $(".kp-mobile-menu").toggleClass("hide show");
        $(el).toggleClass("top-20 top-220");
        $($(el).find("i")).toggleClass("fa-bars fa-times");
    }

    $(".kp-bk-bot-wars-bbt").click(function () {
        showwars(this);
    }); // event on click show hide wars
    $(".kp-ico-rang").click(function () {
        showrang(this);
    }); // event on click show hide rang players
    $(".level-rang").click(function () {
        showlevels_player(this);
    }); // event on click show levels skulls players
    $(".kp-mtnu-bt").click(function () {
        showmobile(this);
    });
    //$(".kp-bk-bot-wars-bbt-m").click(function(){showmobile(this);}); // event on click show mobile menu

    // $(".box").boxLoader({direction: "none", position: "none", effect: "fadeIn", duration: "2s", windowarea: "90%"}); //scroll fadeIn block news!

    ShowQAMsg();

    //new slideshow
    var slides = document.querySelectorAll('#simple_slides .simple_slide');
    var currentSlide = 0;
    var slideInterval = setInterval(function (){
        slides[currentSlide].className = 'simple_slide';
        currentSlide = (currentSlide+1)%slides.length;
        slides[currentSlide].className = 'simple_slide showing';
    }, 4000);

    var $preloader = $('#page-preloader'), $spinner = $preloader.find('.spinner');
    $spinner.fadeOut();
    $preloader.delay(750).fadeOut('slow');

});