! function (e) {
    "use strict";
    var a = e(window),
        i = e("body"),
        t = e(".navbar");

    function l() {
        return a.width()
    }
    "ontouchstart" in document.documentElement || i.addClass("no-touch");
    var s = l();
    a.on("resize", function () {
        s = l()
    });
    var n = e(".is-sticky");
    if (n.length > 0) {
        var r = e("#mainnav").offset();
        a.scroll(function () {
            var e = a.scrollTop();
            a.width() > 991 && e > r.top ? n.hasClass("has-fixed") || n.addClass("has-fixed") : n.hasClass("has-fixed") && n.removeClass("has-fixed")
        })
    }
    e('a.menu-link[href*="#"]:not([href="#"])').on("click", function () {
        if (location.pathname.replace(/^\//, "") === this.pathname.replace(/^\//, "") && location.hostname === this.hostname) {
            var a = e(this.hash),
                i = !!this.hash.slice(1) && e("[name=" + this.hash.slice(1) + "]"),
                l = s >= 992 ? t.height() - 1 : 0;
            if ((a = a.length ? a : i).length) return e("html, body").animate({
                scrollTop: a.offset().top - l
            }, 1e3, "easeInOutExpo"), !1
        }
    });
    var o = window.location.href,
        d = o.split("#"),
        c = e(".nav li a");
    c.length > 0 && c.each(function () {
        o === this.href && "" !== d[1] && e(this).closest("li").addClass("active").parent().closest("li").addClass("active")
    });
    var m = e(".dropdown");
    m.length > 0 && (m.on("mouseover", function () {
        a.width() > 991 && (e(".dropdown-menu", this).not(".in .dropdown-menu").stop().fadeIn("400"), e(this).addClass("open"))
    }), m.on("mouseleave", function () {
        a.width() > 991 && (e(".dropdown-menu", this).not(".in .dropdown-menu").stop().fadeOut("400"), e(this).removeClass("open"))
    }), m.on("click", function () {
        if (a.width() < 991) return e(this).children(".dropdown-menu").fadeToggle(400), e(this).toggleClass("open"), !1
    })), a.on("resize", function () {
        e(".navbar-collapse").removeClass("in"), m.children(".dropdown-menu").fadeOut("400")
    });
    var h = e(".navbar-toggler"),
        u = e(".is-transparent");
    h.length > 0 && h.on("click", function () {
        e(".remove-animation").removeClass("animated"), u.hasClass("active") ? u.removeClass("active") : u.addClass("active")
    });
    var g = e("select");
    g.length > 0 && g.select2(), e(".menu-link").on("click", function () {
        e(".navbar-collapse").collapse("hide"), u.removeClass("active")
    });
    var x = e('[class*="mask-ov"]');
    x.length > 0 && x.each(function () {
        var a = e(this).parent();
        a.hasClass("has-maskbg") || a.addClass("has-maskbg")
    });
    var P = e(".animated");
    e().waypoint && P.length > 0 && a.on("load", function () {
        P.each(function () {
            var a = e(this),
                i = a.data("animate"),
                t = a.data("duration"),
                l = a.data("delay");
            a.waypoint(function () {
                a.addClass("animated " + i).css("visibility", "visible"), t && a.css("animation-duration", t + "s"), l && a.css("animation-delay", l + "s")
            }, {
                    offset: "93%"
                })
        })
    });
}(jQuery);
