! function(t) {
    function e(r) {
        if (n[r]) return n[r].exports;
        var o = n[r] = {
            exports: {},
            id: r,
            loaded: !1
        };
        return t[r].call(o.exports, o, o.exports, e), o.loaded = !0, o.exports
    }
    var n = {};
    return e.m = t, e.c = n, e.p = "", e(0)
}([function(t, e, n) {
    t.exports = n(1)
}, function(t, e, n) {
    "use strict";

    function r(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }
    var o = n(2),
        i = r(o);
    i.default.init(window, "glomex-player")
}, function(t, e, n) {
    "use strict";

    function r(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var o = n(3),
        i = r(o),
        s = n(8),
        a = r(s),
        u = n(9),
        c = r(u),
        l = n(10),
        f = r(l),
        h = n(11),
        d = r(h),
        p = n(12),
        m = r(p),
        v = n(14),
        b = r(v),
        g = n(15),
        y = r(g),
        E = n(17),
        _ = function(t, e) {
            if (!t.__glomexEmbedIsInitialized) {
                var n = (0, m.default)(t, a.default, c.default, f.default, d.default, i.default, E.createUuid);
                (0, y.default)(e, (0, b.default)(n, c.default)), t.__glomexEmbedIsInitialized = !0
            }
        };
    e.default = {
        init: _
    }
}, function(t, e, n) {
    "use strict";

    function r(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var o = n(4),
        i = r(o),
        s = n(6),
        a = r(s);
    n(7);
    var u = (0, i.default)(function(t, e) {
        return new window.IntersectionObserver(t, e)
    });
    e.default = (0, a.default)(u)
}, function(t, e, n) {
    "use strict";

    function r(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var o = n(5),
        i = r(o),
        s = function(t) {
            return function(e) {
                var n = void 0,
                    r = new i.default,
                    o = {
                        threshold: [0, .1, .2, .3, .4, .5, .6, .7, .8, .9, 1]
                    },
                    s = t(function(t) {
                        var e = t[0].intersectionRatio,
                            o = String(parseFloat(e)).substring(0, 3);
                        n = o, r.emit("Visibility Change", o)
                    }, o);
                return s.observe(e), {
                    onVisibilityChange: function(t) {
                        n && setTimeout(function() {
                            t(n)
                        }, 0), r.on("Visibility Change", t)
                    }
                }
            }
        };
    e.default = s
}, function(t, e, n) {
    "use strict";

    function r() {}

    function o(t, e, n) {
        this.fn = t, this.context = e, this.once = n || !1
    }

    function i() {
        this._events = new r, this._eventsCount = 0
    }
    var s = Object.prototype.hasOwnProperty,
        a = "~";
    Object.create && (r.prototype = Object.create(null), (new r).__proto__ || (a = !1)), i.prototype.eventNames = function() {
        var t, e, n = [];
        if (0 === this._eventsCount) return n;
        for (e in t = this._events) s.call(t, e) && n.push(a ? e.slice(1) : e);
        return Object.getOwnPropertySymbols ? n.concat(Object.getOwnPropertySymbols(t)) : n
    }, i.prototype.listeners = function(t, e) {
        var n = a ? a + t : t,
            r = this._events[n];
        if (e) return !!r;
        if (!r) return [];
        if (r.fn) return [r.fn];
        for (var o = 0, i = r.length, s = new Array(i); o < i; o++) s[o] = r[o].fn;
        return s
    }, i.prototype.emit = function(t, e, n, r, o, i) {
        var s = a ? a + t : t;
        if (!this._events[s]) return !1;
        var u, c, l = this._events[s],
            f = arguments.length;
        if (l.fn) {
            switch (l.once && this.removeListener(t, l.fn, void 0, !0), f) {
                case 1:
                    return l.fn.call(l.context), !0;
                case 2:
                    return l.fn.call(l.context, e), !0;
                case 3:
                    return l.fn.call(l.context, e, n), !0;
                case 4:
                    return l.fn.call(l.context, e, n, r), !0;
                case 5:
                    return l.fn.call(l.context, e, n, r, o), !0;
                case 6:
                    return l.fn.call(l.context, e, n, r, o, i), !0
            }
            for (c = 1, u = new Array(f - 1); c < f; c++) u[c - 1] = arguments[c];
            l.fn.apply(l.context, u)
        } else {
            var h, d = l.length;
            for (c = 0; c < d; c++) switch (l[c].once && this.removeListener(t, l[c].fn, void 0, !0), f) {
                case 1:
                    l[c].fn.call(l[c].context);
                    break;
                case 2:
                    l[c].fn.call(l[c].context, e);
                    break;
                case 3:
                    l[c].fn.call(l[c].context, e, n);
                    break;
                case 4:
                    l[c].fn.call(l[c].context, e, n, r);
                    break;
                default:
                    if (!u)
                        for (h = 1, u = new Array(f - 1); h < f; h++) u[h - 1] = arguments[h];
                    l[c].fn.apply(l[c].context, u)
            }
        }
        return !0
    }, i.prototype.on = function(t, e, n) {
        var r = new o(e, n || this),
            i = a ? a + t : t;
        return this._events[i] ? this._events[i].fn ? this._events[i] = [this._events[i], r] : this._events[i].push(r) : (this._events[i] = r, this._eventsCount++), this
    }, i.prototype.once = function(t, e, n) {
        var r = new o(e, n || this, !0),
            i = a ? a + t : t;
        return this._events[i] ? this._events[i].fn ? this._events[i] = [this._events[i], r] : this._events[i].push(r) : (this._events[i] = r, this._eventsCount++), this
    }, i.prototype.removeListener = function(t, e, n, o) {
        var i = a ? a + t : t;
        if (!this._events[i]) return this;
        if (!e) return 0 === --this._eventsCount ? this._events = new r : delete this._events[i], this;
        var s = this._events[i];
        if (s.fn) s.fn !== e || o && !s.once || n && s.context !== n || (0 === --this._eventsCount ? this._events = new r : delete this._events[i]);
        else {
            for (var u = 0, c = [], l = s.length; u < l; u++)(s[u].fn !== e || o && !s[u].once || n && s[u].context !== n) && c.push(s[u]);
            c.length ? this._events[i] = 1 === c.length ? c[0] : c : 0 === --this._eventsCount ? this._events = new r : delete this._events[i]
        }
        return this
    }, i.prototype.removeAllListeners = function(t) {
        var e;
        return t ? (e = a ? a + t : t, this._events[e] && (0 === --this._eventsCount ? this._events = new r : delete this._events[e])) : (this._events = new r, this._eventsCount = 0), this
    }, i.prototype.off = i.prototype.removeListener, i.prototype.addListener = i.prototype.on, i.prototype.setMaxListeners = function() {
        return this
    }, i.prefixed = a, i.EventEmitter = i, t.exports = i
}, function(t, e) {
    "use strict";
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var n = function(t) {
        return {
            observe: function(e) {
                return t(e)
            }
        }
    };
    e.default = n
}, function(t, e) {
    ! function(t, e) {
        "use strict";

        function n(t) {
            this.time = t.time, this.target = t.target, this.rootBounds = t.rootBounds, this.boundingClientRect = t.boundingClientRect, this.intersectionRect = t.intersectionRect || l(), this.isIntersecting = !!t.intersectionRect;
            var e = this.boundingClientRect,
                n = e.width * e.height,
                r = this.intersectionRect,
                o = r.width * r.height;
            n ? this.intersectionRatio = o / n : this.intersectionRatio = this.isIntersecting ? 1 : 0
        }

        function r(t, e) {
            var n = e || {};
            if ("function" != typeof t) throw new Error("callback must be a function");
            if (n.root && 1 != n.root.nodeType) throw new Error("root must be an Element");
            this._checkForIntersections = i(this._checkForIntersections.bind(this), this.THROTTLE_TIMEOUT), this._callback = t, this._observationTargets = [], this._queuedEntries = [], this._rootMarginValues = this._parseRootMargin(n.rootMargin), this.thresholds = this._initThresholds(n.threshold), this.root = n.root || null, this.rootMargin = this._rootMarginValues.map(function(t) {
                return t.value + t.unit
            }).join(" ")
        }

        function o() {
            return t.performance && performance.now && performance.now()
        }

        function i(t, e) {
            var n = null;
            return function() {
                n || (n = setTimeout(function() {
                    t(), n = null
                }, e))
            }
        }

        function s(t, e, n, r) {
            "function" == typeof t.addEventListener ? t.addEventListener(e, n, r || !1) : "function" == typeof t.attachEvent && t.attachEvent("on" + e, n)
        }

        function a(t, e, n, r) {
            "function" == typeof t.removeEventListener ? t.removeEventListener(e, n, r || !1) : "function" == typeof t.detatchEvent && t.detatchEvent("on" + e, n)
        }

        function u(t, e) {
            var n = Math.max(t.top, e.top),
                r = Math.min(t.bottom, e.bottom),
                o = Math.max(t.left, e.left),
                i = Math.min(t.right, e.right),
                s = i - o,
                a = r - n;
            return s >= 0 && a >= 0 && {
                top: n,
                bottom: r,
                left: o,
                right: i,
                width: s,
                height: a
            }
        }

        function c(t) {
            var e;
            try {
                e = t.getBoundingClientRect()
            } catch (t) {}
            return e ? (e.width && e.height || (e = {
                top: e.top,
                right: e.right,
                bottom: e.bottom,
                left: e.left,
                width: e.right - e.left,
                height: e.bottom - e.top
            }), e) : l()
        }

        function l() {
            return {
                top: 0,
                bottom: 0,
                left: 0,
                right: 0,
                width: 0,
                height: 0
            }
        }

        function f(t, e) {
            for (var n = e; n;) {
                if (11 == n.nodeType && n.host && (n = n.host), n == t) return !0;
                n = n.parentNode
            }
            return !1
        }
        if (!("IntersectionObserver" in t && "IntersectionObserverEntry" in t && "intersectionRatio" in t.IntersectionObserverEntry.prototype)) {
            var h = [];
            r.prototype.THROTTLE_TIMEOUT = 100, r.prototype.POLL_INTERVAL = null, r.prototype.observe = function(t) {
                if (!this._observationTargets.some(function(e) {
                        return e.element == t
                    })) {
                    if (!t || 1 != t.nodeType) throw new Error("target must be an Element");
                    this._registerInstance(), this._observationTargets.push({
                        element: t,
                        entry: null
                    }), this._monitorIntersections()
                }
            }, r.prototype.unobserve = function(t) {
                this._observationTargets = this._observationTargets.filter(function(e) {
                    return e.element != t
                }), this._observationTargets.length || (this._unmonitorIntersections(), this._unregisterInstance())
            }, r.prototype.disconnect = function() {
                this._observationTargets = [], this._unmonitorIntersections(), this._unregisterInstance()
            }, r.prototype.takeRecords = function() {
                var t = this._queuedEntries.slice();
                return this._queuedEntries = [], t
            }, r.prototype._initThresholds = function(t) {
                var e = t || [0];
                return Array.isArray(e) || (e = [e]), e.sort().filter(function(t, e, n) {
                    if ("number" != typeof t || isNaN(t) || t < 0 || t > 1) throw new Error("threshold must be a number between 0 and 1 inclusively");
                    return t !== n[e - 1]
                })
            }, r.prototype._parseRootMargin = function(t) {
                var e = t || "0px",
                    n = e.split(/\s+/).map(function(t) {
                        var e = /^(-?\d*\.?\d+)(px|%)$/.exec(t);
                        if (!e) throw new Error("rootMargin must be specified in pixels or percent");
                        return {
                            value: parseFloat(e[1]),
                            unit: e[2]
                        }
                    });
                return n[1] = n[1] || n[0], n[2] = n[2] || n[0], n[3] = n[3] || n[1], n
            }, r.prototype._monitorIntersections = function() {
                this._monitoringIntersections || (this._monitoringIntersections = !0, this._checkForIntersections(), this.POLL_INTERVAL ? this._monitoringInterval = setInterval(this._checkForIntersections, this.POLL_INTERVAL) : (s(t, "resize", this._checkForIntersections, !0), s(e, "scroll", this._checkForIntersections, !0), "MutationObserver" in t && (this._domObserver = new MutationObserver(this._checkForIntersections), this._domObserver.observe(e, {
                    attributes: !0,
                    childList: !0,
                    characterData: !0,
                    subtree: !0
                }))))
            }, r.prototype._unmonitorIntersections = function() {
                this._monitoringIntersections && (this._monitoringIntersections = !1, clearInterval(this._monitoringInterval), this._monitoringInterval = null, a(t, "resize", this._checkForIntersections, !0), a(e, "scroll", this._checkForIntersections, !0), this._domObserver && (this._domObserver.disconnect(), this._domObserver = null))
            }, r.prototype._checkForIntersections = function() {
                var t = this._rootIsInDom(),
                    e = t ? this._getRootRect() : l();
                this._observationTargets.forEach(function(r) {
                    var i = r.element,
                        s = c(i),
                        a = this._rootContainsTarget(i),
                        u = r.entry,
                        l = t && a && this._computeTargetAndRootIntersection(i, e),
                        f = r.entry = new n({
                            time: o(),
                            target: i,
                            boundingClientRect: s,
                            rootBounds: e,
                            intersectionRect: l
                        });
                    u ? t && a ? this._hasCrossedThreshold(u, f) && this._queuedEntries.push(f) : u && u.isIntersecting && this._queuedEntries.push(f) : this._queuedEntries.push(f)
                }, this), this._queuedEntries.length && this._callback(this.takeRecords(), this)
            }, r.prototype._computeTargetAndRootIntersection = function(n, r) {
                if ("none" != t.getComputedStyle(n).display) {
                    for (var o = c(n), i = o, s = n.parentNode, a = !1; !a;) {
                        var l = null;
                        if (s == this.root || s == e.body || s == e.documentElement || 1 != s.nodeType ? (a = !0, l = r) : "visible" != t.getComputedStyle(s).overflow && (l = c(s)), l && (i = u(l, i), !i)) break;
                        s = s.parentNode
                    }
                    return i
                }
            }, r.prototype._getRootRect = function() {
                var t;
                if (this.root) t = c(this.root);
                else {
                    var n = e.documentElement,
                        r = e.body;
                    t = {
                        top: 0,
                        left: 0,
                        right: n.clientWidth || r.clientWidth,
                        width: n.clientWidth || r.clientWidth,
                        bottom: n.clientHeight || r.clientHeight,
                        height: n.clientHeight || r.clientHeight
                    }
                }
                return this._expandRectByRootMargin(t)
            }, r.prototype._expandRectByRootMargin = function(t) {
                var e = this._rootMarginValues.map(function(e, n) {
                        return "px" == e.unit ? e.value : e.value * (n % 2 ? t.width : t.height) / 100
                    }),
                    n = {
                        top: t.top - e[0],
                        right: t.right + e[1],
                        bottom: t.bottom + e[2],
                        left: t.left - e[3]
                    };
                return n.width = n.right - n.left, n.height = n.bottom - n.top, n
            }, r.prototype._hasCrossedThreshold = function(t, e) {
                var n = t && t.isIntersecting ? t.intersectionRatio || 0 : -1,
                    r = e.isIntersecting ? e.intersectionRatio || 0 : -1;
                if (n !== r)
                    for (var o = 0; o < this.thresholds.length; o++) {
                        var i = this.thresholds[o];
                        if (i == n || i == r || i < n != i < r) return !0
                    }
            }, r.prototype._rootIsInDom = function() {
                return !this.root || f(e, this.root)
            }, r.prototype._rootContainsTarget = function(t) {
                return f(this.root || e, t)
            }, r.prototype._registerInstance = function() {
                h.indexOf(this) < 0 && h.push(this)
            }, r.prototype._unregisterInstance = function() {
                var t = h.indexOf(this);
                t != -1 && h.splice(t, 1)
            }, t.IntersectionObserver = r, t.IntersectionObserverEntry = n
        }
    }(window, document)
}, function(t, e) {
    "use strict";

    function n(t, e) {
        var n = !0;
        return e.forEach(function(e) {
            n = !!t.getAttribute(e)
        }), n
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    }), e.default = n
}, function(t, e) {
    "use strict";

    function n() {
        return Array.prototype.slice.call(r)
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    }), e.default = n;
    var r = ["data-player-id", "data-playlist-id", "data-width", "data-height"]
}, function(t, e) {
    "use strict";

    function n(t, e, n) {
        var i = ["playerId=" + t, "playlistId=" + e],
            s = r;
        return t.indexOf("stage") !== -1 && (s = o), n && i.push("waitForInit=1"), s + "?" + i.join("&")
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    }), e.default = n;
    var r = "//component-vvs.glomex.com/embed/1/index.html",
        o = "//component-ci.vvs.glomex.cloud/embed-site/latest/index.html"
}, function(t, e) {
    "use strict";

    function n(t, e, n, r) {
        var o = t.createElement("iframe");
		
		var domElement = o;
		console.log(domElement);
        return o.setAttribute("frameborder", "0"), o.setAttribute("allowfullscreen", "true"), o.setAttribute("width", n), o.setAttribute("height", r), o.setAttribute("src", e), o
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    }), e.default = n
}, function(t, e, n) {
    "use strict";

    function r(t, e, n, r, u, c, l) {
        return function(f) {
            if (!i(f) && e(f, n())) {
                var h = f.getAttribute("data-player-id"),
                    d = f.getAttribute("data-playlist-id"),
                    p = f.getAttribute("data-width"),
                    m = f.getAttribute("data-height"),
                    v = r(h, d, !0),
                    b = u(t.document, v, p, m),
                    g = c.observe(b);
                b.addEventListener("load", function() {
                    var t = b.contentWindow;
                    t.postMessage(JSON.stringify({
                        name: s,
                        glomexContext: {
                            playerSessionId: l()
                        }
                    }), "*"), g.onVisibilityChange(function(e) {
                        t.postMessage(JSON.stringify({
                            name: a,
                            visibility: e
                        }), "*")
                    })
                }), t.addEventListener("message", function(t) {
                    var e = t.data,
                        n = void 0 === e ? {} : e,
                        r = t.source,
                        i = n.eventName,
                        s = n.trackedEvent;
                    r === b.contentWindow && "trackingevent" === i && f.dispatchEvent(new o("trackingevent", {
                        detail: s
                    }))
                }), f.appendChild(b)
            }
        }
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    }), e.default = r;
    var o = n(13),
        i = function(t) {
            return t.children[0] instanceof HTMLIFrameElement
        },
        s = "Init",
        a = "Visibility Change"
}, function(t, e) {
    (function(e) {
        function n() {
            try {
                var t = new r("cat", {
                    detail: {
                        foo: "bar"
                    }
                });
                return "cat" === t.type && "bar" === t.detail.foo
            } catch (t) {}
            return !1
        }
        var r = e.CustomEvent;
        t.exports = n() ? r : "undefined" != typeof document && "function" == typeof document.createEvent ? function(t, e) {
            var n = document.createEvent("CustomEvent");
            return e ? n.initCustomEvent(t, e.bubbles, e.cancelable, e.detail) : n.initCustomEvent(t, !1, !1, void 0), n
        } : function(t, e) {
            var n = document.createEventObject();
            return n.type = t, e ? (n.bubbles = Boolean(e.bubbles), n.cancelable = Boolean(e.cancelable), n.detail = e.detail) : (n.bubbles = !1, n.cancelable = !1, n.detail = void 0), n
        }
    }).call(e, function() {
        return this
    }())
}, function(t, e) {
    "use strict";

    function n(t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
    }

    function r(t, e) {
        if (!t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return !e || "object" != typeof e && "function" != typeof e ? t : e
    }

    function o(t, e) {
        if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function, not " + typeof e);
        t.prototype = Object.create(e && e.prototype, {
            constructor: {
                value: t,
                enumerable: !1,
                writable: !0,
                configurable: !0
            }
        }), e && (Object.setPrototypeOf ? Object.setPrototypeOf(t, e) : t.__proto__ = e)
    }

    function i(t, e) {
        var i = function(i) {
            function a(t) {
                var e, o;
                return n(this, a), t = e = r(this, (a.__proto__ || Object.getPrototypeOf(a)).call(this, t)), o = t, r(e, o)
            }
            return o(a, i), s(a, [{
                key: "connectedCallback",
                value: function() {
                    t(this)
                }
            }, {
                key: "attributeChangedCallback",
                value: function() {
                    t(this)
                }
            }], [{
                key: "observedAttributes",
                get: function() {
                    return e()
                }
            }]), a
        }(HTMLElement);
        return i
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var s = function() {
        function t(t, e) {
            for (var n = 0; n < e.length; n++) {
                var r = e[n];
                r.enumerable = r.enumerable || !1, r.configurable = !0, "value" in r && (r.writable = !0), Object.defineProperty(t, r.key, r)
            }
        }
        return function(e, n, r) {
            return n && t(e.prototype, n), r && t(e, r), e
        }
    }();
    e.default = i
}, function(t, e, n) {
    "use strict";

    function r(t, e) {
        try {
            customElements.define(t, e)
        } catch (t) {}
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    }), e.default = r, n(16)
}, function(t, e) {
    (function(e) {
        /*!
        	
        	Copyright (C) 2014-2016 by Andrea Giammarchi - @WebReflection
        	
        	Permission is hereby granted, free of charge, to any person obtaining a copy
        	of this software and associated documentation files (the "Software"), to deal
        	in the Software without restriction, including without limitation the rights
        	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
        	copies of the Software, and to permit persons to whom the Software is
        	furnished to do so, subject to the following conditions:
        	
        	The above copyright notice and this permission notice shall be included in
        	all copies or substantial portions of the Software.
        	
        	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
        	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
        	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
        	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
        	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
        	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
        	THE SOFTWARE.
        	
        	*/
        function n(t) {
            "use strict";

            function e() {
                var t = T.splice(0, T.length);
                for (Jt = 0; t.length;) t.shift().call(null, t.shift())
            }

            function n(t, e) {
                for (var n = 0, r = t.length; n < r; n++) p(t[n], e)
            }

            function r(t) {
                for (var e, n = 0, r = t.length; n < r; n++) e = t[n], C(e, rt[i(e)])
            }

            function o(t) {
                return function(e) {
                    Pt(e) && (p(e, t), n(e.querySelectorAll(ot), t))
                }
            }

            function i(t) {
                var e = jt.call(t, "is"),
                    n = t.nodeName.toUpperCase(),
                    r = st.call(nt, e ? Y + e.toUpperCase() : Q + n);
                return e && -1 < r && !s(n, e) ? -1 : r
            }

            function s(t, e) {
                return -1 < ot.indexOf(t + '[is="' + e + '"]')
            }

            function a(t) {
                var e = t.currentTarget,
                    n = t.attrChange,
                    r = t.attrName,
                    o = t.target,
                    i = t[Z] || 2,
                    s = t[J] || 3;
                !Qt || o && o !== e || !e[j] || "style" === r || t.prevValue === t.newValue && ("" !== t.newValue || n !== i && n !== s) || e[j](r, n === i ? null : t.prevValue, n === s ? null : t.newValue)
            }

            function u(t) {
                var n = o(t);
                return function(t) {
                    T.push(n, t.target), Jt && clearTimeout(Jt), Jt = setTimeout(e, 1)
                }
            }

            function c(t) {
                $t && ($t = !1, t.currentTarget.removeEventListener(X, c)), n((t.target || I).querySelectorAll(ot), t.detail === D ? D : F), Nt && h()
            }

            function l(t, e) {
                var n = this;
                Bt.call(n, t, e), M.call(n, {
                    target: n
                })
            }

            function f(t, e) {
                It(t, e), H ? H.observe(t, Zt) : (Xt && (t.setAttribute = l, t[k] = w(t), t[P]($, M)), t[P](K, a)), t[W] && Qt && (t.created = !0, t[W](), t.created = !1)
            }

            function h() {
                for (var t, e = 0, n = Ft.length; e < n; e++) t = Ft[e], it.contains(t) || (n--, Ft.splice(e--, 1), p(t, D))
            }

            function d(t) {
                throw new Error("A " + t + " type is already registered")
            }

            function p(t, e) {
                var n, r = i(t); - 1 < r && (x(t, rt[r]), r = 0, e !== F || t[F] ? e !== D || t[D] || (t[F] = !1, t[D] = !0, r = 1) : (t[D] = !1, t[F] = !0, r = 1, Nt && st.call(Ft, t) < 0 && Ft.push(t)), r && (n = t[e + S]) && n.call(t))
            }

            function m() {}

            function v(t, e, n) {
                var r = n && n[V] || "",
                    o = e.prototype,
                    i = Ct(o),
                    s = e.observedAttributes || ft,
                    a = {
                        prototype: i
                    };
                kt(i, W, {
                    value: function() {
                        if (Lt) Lt = !1;
                        else if (!this[gt]) {
                            this[gt] = !0, new e(this), o[W] && o[W].call(this);
                            var t = wt[Ot.get(e)];
                            (!Et || t.create.length > 1) && y(this)
                        }
                    }
                }), kt(i, j, {
                    value: function(t) {
                        -1 < st.call(s, t) && o[j].apply(this, arguments)
                    }
                }), o[q] && kt(i, U, {
                    value: o[q]
                }), o[B] && kt(i, z, {
                    value: o[B]
                }), r && (a[V] = r), t = t.toUpperCase(), wt[t] = {
                    constructor: e,
                    create: r ? [r, xt(t)] : [t]
                }, Ot.set(e, t), I[N](t.toLowerCase(), a), E(t), Ht[t].r()
            }

            function b(t) {
                var e = wt[t.toUpperCase()];
                return e && e.constructor
            }

            function g(t) {
                return "string" == typeof t ? t : t && t.is || ""
            }

            function y(t) {
                for (var e, n = t[j], r = n ? t.attributes : ft, o = r.length; o--;) e = r[o], n.call(t, e.name || e.nodeName, null, e.value || e.nodeValue)
            }

            function E(t) {
                return t = t.toUpperCase(), t in Ht || (Ht[t] = {}, Ht[t].p = new Mt(function(e) {
                    Ht[t].r = e
                })), Ht[t].p
            }

            function _() {
                yt && delete t.customElements, lt(t, "customElements", {
                    configurable: !0,
                    value: new m
                }), lt(t, "CustomElementRegistry", {
                    configurable: !0,
                    value: m
                });
                for (var e = function(e) {
                        var n = t[e];
                        if (n) {
                            t[e] = function(t) {
                                var e, r;
                                return t || (t = this), t[gt] || (Lt = !0, e = wt[Ot.get(t.constructor)], r = Et && 1 === e.create.length, t = r ? Reflect.construct(n, ft, e.constructor) : I.createElement.apply(I, e.create), t[gt] = !0, Lt = !1, r || y(t)), t
                            }, t[e].prototype = n.prototype;
                            try {
                                n.prototype.constructor = t[e]
                            } catch (r) {
                                bt = !0, lt(n, gt, {
                                    value: t[e]
                                })
                            }
                        }
                    }, n = R.get(/^HTML[A-Z]*[a-z]/), r = n.length; r--; e(n[r]));
                I.createElement = function(t, e) {
                    var n = g(e);
                    return n ? zt.call(this, t, xt(n)) : zt.call(this, t)
                }
            }
            var T, M, L, w, H, O, x, C, I = t.document,
                A = t.Object,
                R = function(t) {
                    var e, n, r, o, i = /^[A-Z]+[a-z]/,
                        s = function(t) {
                            var e, n = [];
                            for (e in u) t.test(e) && n.push(e);
                            return n
                        },
                        a = function(t, e) {
                            e = e.toLowerCase(), e in u || (u[t] = (u[t] || []).concat(e), u[e] = u[e.toUpperCase()] = t)
                        },
                        u = (A.create || A)(null),
                        c = {};
                    for (n in t)
                        for (o in t[n])
                            for (r = t[n][o], u[o] = r, e = 0; e < r.length; e++) u[r[e].toLowerCase()] = u[r[e].toUpperCase()] = o;
                    return c.get = function(t) {
                        return "string" == typeof t ? u[t] || (i.test(t) ? [] : "") : s(t)
                    }, c.set = function(t, e) {
                        return i.test(t) ? a(t, e) : a(e, t), c
                    }, c
                }({
                    collections: {
                        HTMLAllCollection: ["all"],
                        HTMLCollection: ["forms"],
                        HTMLFormControlsCollection: ["elements"],
                        HTMLOptionsCollection: ["options"]
                    },
                    elements: {
                        Element: ["element"],
                        HTMLAnchorElement: ["a"],
                        HTMLAppletElement: ["applet"],
                        HTMLAreaElement: ["area"],
                        HTMLAttachmentElement: ["attachment"],
                        HTMLAudioElement: ["audio"],
                        HTMLBRElement: ["br"],
                        HTMLBaseElement: ["base"],
                        HTMLBodyElement: ["body"],
                        HTMLButtonElement: ["button"],
                        HTMLCanvasElement: ["canvas"],
                        HTMLContentElement: ["content"],
                        HTMLDListElement: ["dl"],
                        HTMLDataElement: ["data"],
                        HTMLDataListElement: ["datalist"],
                        HTMLDetailsElement: ["details"],
                        HTMLDialogElement: ["dialog"],
                        HTMLDirectoryElement: ["dir"],
                        HTMLDivElement: ["div"],
                        HTMLDocument: ["document"],
                        HTMLElement: ["element", "abbr", "address", "article", "aside", "b", "bdi", "bdo", "cite", "code", "command", "dd", "dfn", "dt", "em", "figcaption", "figure", "footer", "header", "i", "kbd", "mark", "nav", "noscript", "rp", "rt", "ruby", "s", "samp", "section", "small", "strong", "sub", "summary", "sup", "u", "var", "wbr"],
                        HTMLEmbedElement: ["embed"],
                        HTMLFieldSetElement: ["fieldset"],
                        HTMLFontElement: ["font"],
                        HTMLFormElement: ["form"],
                        HTMLFrameElement: ["frame"],
                        HTMLFrameSetElement: ["frameset"],
                        HTMLHRElement: ["hr"],
                        HTMLHeadElement: ["head"],
                        HTMLHeadingElement: ["h1", "h2", "h3", "h4", "h5", "h6"],
                        HTMLHtmlElement: ["html"],
                        HTMLIFrameElement: ["iframe"],
                        HTMLImageElement: ["img"],
                        HTMLInputElement: ["input"],
                        HTMLKeygenElement: ["keygen"],
                        HTMLLIElement: ["li"],
                        HTMLLabelElement: ["label"],
                        HTMLLegendElement: ["legend"],
                        HTMLLinkElement: ["link"],
                        HTMLMapElement: ["map"],
                        HTMLMarqueeElement: ["marquee"],
                        HTMLMediaElement: ["media"],
                        HTMLMenuElement: ["menu"],
                        HTMLMenuItemElement: ["menuitem"],
                        HTMLMetaElement: ["meta"],
                        HTMLMeterElement: ["meter"],
                        HTMLModElement: ["del", "ins"],
                        HTMLOListElement: ["ol"],
                        HTMLObjectElement: ["object"],
                        HTMLOptGroupElement: ["optgroup"],
                        HTMLOptionElement: ["option"],
                        HTMLOutputElement: ["output"],
                        HTMLParagraphElement: ["p"],
                        HTMLParamElement: ["param"],
                        HTMLPictureElement: ["picture"],
                        HTMLPreElement: ["pre"],
                        HTMLProgressElement: ["progress"],
                        HTMLQuoteElement: ["blockquote", "q", "quote"],
                        HTMLScriptElement: ["script"],
                        HTMLSelectElement: ["select"],
                        HTMLShadowElement: ["shadow"],
                        HTMLSlotElement: ["slot"],
                        HTMLSourceElement: ["source"],
                        HTMLSpanElement: ["span"],
                        HTMLStyleElement: ["style"],
                        HTMLTableCaptionElement: ["caption"],
                        HTMLTableCellElement: ["td", "th"],
                        HTMLTableColElement: ["col", "colgroup"],
                        HTMLTableElement: ["table"],
                        HTMLTableRowElement: ["tr"],
                        HTMLTableSectionElement: ["thead", "tbody", "tfoot"],
                        HTMLTemplateElement: ["template"],
                        HTMLTextAreaElement: ["textarea"],
                        HTMLTimeElement: ["time"],
                        HTMLTitleElement: ["title"],
                        HTMLTrackElement: ["track"],
                        HTMLUListElement: ["ul"],
                        HTMLUnknownElement: ["unknown", "vhgroupv", "vkeygen"],
                        HTMLVideoElement: ["video"]
                    },
                    nodes: {
                        Attr: ["node"],
                        Audio: ["audio"],
                        CDATASection: ["node"],
                        CharacterData: ["node"],
                        Comment: ["#comment"],
                        Document: ["#document"],
                        DocumentFragment: ["#document-fragment"],
                        DocumentType: ["node"],
                        HTMLDocument: ["#document"],
                        Image: ["img"],
                        Option: ["option"],
                        ProcessingInstruction: ["node"],
                        ShadowRoot: ["#shadow-root"],
                        Text: ["#text"],
                        XMLDocument: ["xml"]
                    }
                }),
                N = "registerElement",
                k = "__" + N + (1e5 * t.Math.random() >> 0),
                P = "addEventListener",
                F = "attached",
                S = "Callback",
                D = "detached",
                V = "extends",
                j = "attributeChanged" + S,
                U = F + S,
                q = "connected" + S,
                B = "disconnected" + S,
                W = "created" + S,
                z = D + S,
                Z = "ADDITION",
                G = "MODIFICATION",
                J = "REMOVAL",
                K = "DOMAttrModified",
                X = "DOMContentLoaded",
                $ = "DOMSubtreeModified",
                Q = "<",
                Y = "=",
                tt = /^[A-Z][A-Z0-9]*(?:-[A-Z0-9]+)+$/,
                et = ["ANNOTATION-XML", "COLOR-PROFILE", "FONT-FACE", "FONT-FACE-SRC", "FONT-FACE-URI", "FONT-FACE-FORMAT", "FONT-FACE-NAME", "MISSING-GLYPH"],
                nt = [],
                rt = [],
                ot = "",
                it = I.documentElement,
                st = nt.indexOf || function(t) {
                    for (var e = this.length; e-- && this[e] !== t;);
                    return e
                },
                at = A.prototype,
                ut = at.hasOwnProperty,
                ct = at.isPrototypeOf,
                lt = A.defineProperty,
                ft = [],
                ht = A.getOwnPropertyDescriptor,
                dt = A.getOwnPropertyNames,
                pt = A.getPrototypeOf,
                mt = A.setPrototypeOf,
                vt = !!A.__proto__,
                bt = !1,
                gt = "__dreCEv1",
                yt = t.customElements,
                Et = !!(yt && yt.define && yt.get && yt.whenDefined),
                _t = A.create || A,
                Tt = t.Map || function() {
                    var t, e = [],
                        n = [];
                    return {
                        get: function(t) {
                            return n[st.call(e, t)]
                        },
                        set: function(r, o) {
                            t = st.call(e, r), t < 0 ? n[e.push(r) - 1] = o : n[t] = o
                        }
                    }
                },
                Mt = t.Promise || function(t) {
                    function e(t) {
                        for (r = !0; n.length;) n.shift()(t)
                    }
                    var n = [],
                        r = !1,
                        o = {
                            catch: function() {
                                return o
                            },
                            then: function(t) {
                                return n.push(t), r && setTimeout(e, 1), o
                            }
                        };
                    return t(e), o
                },
                Lt = !1,
                wt = _t(null),
                Ht = _t(null),
                Ot = new Tt,
                xt = String,
                Ct = A.create || function t(e) {
                    return e ? (t.prototype = e, new t) : this
                },
                It = mt || (vt ? function(t, e) {
                    return t.__proto__ = e, t
                } : dt && ht ? function() {
                    function t(t, e) {
                        for (var n, r = dt(e), o = 0, i = r.length; o < i; o++) n = r[o], ut.call(t, n) || lt(t, n, ht(e, n))
                    }
                    return function(e, n) {
                        do t(e, n); while ((n = pt(n)) && !ct.call(n, e));
                        return e
                    }
                }() : function(t, e) {
                    for (var n in e) t[n] = e[n];
                    return t
                }),
                At = t.MutationObserver || t.WebKitMutationObserver,
                Rt = (t.HTMLElement || t.Element || t.Node).prototype,
                Nt = !ct.call(Rt, it),
                kt = Nt ? function(t, e, n) {
                    return t[e] = n.value, t
                } : lt,
                Pt = Nt ? function(t) {
                    return 1 === t.nodeType
                } : function(t) {
                    return ct.call(Rt, t)
                },
                Ft = Nt && [],
                St = Rt.attachShadow,
                Dt = Rt.cloneNode,
                Vt = Rt.dispatchEvent,
                jt = Rt.getAttribute,
                Ut = Rt.hasAttribute,
                qt = Rt.removeAttribute,
                Bt = Rt.setAttribute,
                Wt = I.createElement,
                zt = Wt,
                Zt = At && {
                    attributes: !0,
                    characterData: !0,
                    attributeOldValue: !0
                },
                Gt = At || function(t) {
                    Xt = !1, it.removeEventListener(K, Gt)
                },
                Jt = 0,
                Kt = !1,
                Xt = !0,
                $t = !0,
                Qt = !0;
            if (N in I || (mt || vt ? (x = function(t, e) {
                    ct.call(e, t) || f(t, e)
                }, C = f) : (x = function(t, e) {
                    t[k] || (t[k] = A(!0), f(t, e))
                }, C = x), Nt ? (Xt = !1, function() {
                    var t = ht(Rt, P),
                        e = t.value,
                        n = function(t) {
                            var e = new CustomEvent(K, {
                                bubbles: !0
                            });
                            e.attrName = t, e.prevValue = jt.call(this, t), e.newValue = null, e[J] = e.attrChange = 2, qt.call(this, t), Vt.call(this, e)
                        },
                        r = function(t, e) {
                            var n = Ut.call(this, t),
                                r = n && jt.call(this, t),
                                o = new CustomEvent(K, {
                                    bubbles: !0
                                });
                            Bt.call(this, t, e), o.attrName = t, o.prevValue = n ? r : null, o.newValue = e, n ? o[G] = o.attrChange = 1 : o[Z] = o.attrChange = 0, Vt.call(this, o)
                        },
                        o = function(t) {
                            var e, n = t.currentTarget,
                                r = n[k],
                                o = t.propertyName;
                            r.hasOwnProperty(o) && (r = r[o], e = new CustomEvent(K, {
                                bubbles: !0
                            }), e.attrName = r.name, e.prevValue = r.value || null, e.newValue = r.value = n[o] || null, null == e.prevValue ? e[Z] = e.attrChange = 0 : e[G] = e.attrChange = 1, Vt.call(n, e))
                        };
                    t.value = function(t, i, s) {
                        t === K && this[j] && this.setAttribute !== r && (this[k] = {
                            className: {
                                name: "class",
                                value: this.className
                            }
                        }, this.setAttribute = r, this.removeAttribute = n, e.call(this, "propertychange", o)), e.call(this, t, i, s)
                    }, lt(Rt, P, t)
                }()) : At || (it[P](K, Gt), it.setAttribute(k, 1), it.removeAttribute(k), Xt && (M = function(t) {
                    var e, n, r, o = this;
                    if (o === t.target) {
                        e = o[k], o[k] = n = w(o);
                        for (r in n) {
                            if (!(r in e)) return L(0, o, r, e[r], n[r], Z);
                            if (n[r] !== e[r]) return L(1, o, r, e[r], n[r], G)
                        }
                        for (r in e)
                            if (!(r in n)) return L(2, o, r, e[r], n[r], J)
                    }
                }, L = function(t, e, n, r, o, i) {
                    var s = {
                        attrChange: t,
                        currentTarget: e,
                        attrName: n,
                        prevValue: r,
                        newValue: o
                    };
                    s[i] = t, a(s)
                }, w = function(t) {
                    for (var e, n, r = {}, o = t.attributes, i = 0, s = o.length; i < s; i++) e = o[i], n = e.name, "setAttribute" !== n && (r[n] = e.value);
                    return r
                })), I[N] = function(t, e) {
                    if (s = t.toUpperCase(), Kt || (Kt = !0, At ? (H = function(t, e) {
                            function n(t, e) {
                                for (var n = 0, r = t.length; n < r; e(t[n++]));
                            }
                            return new At(function(r) {
                                for (var o, i, s, a = 0, u = r.length; a < u; a++) o = r[a], "childList" === o.type ? (n(o.addedNodes, t), n(o.removedNodes, e)) : (i = o.target, Qt && i[j] && "style" !== o.attributeName && (s = jt.call(i, o.attributeName), s !== o.oldValue && i[j](o.attributeName, o.oldValue, s)))
                            })
                        }(o(F), o(D)), O = function(t) {
                            return H.observe(t, {
                                childList: !0,
                                subtree: !0
                            }), t
                        }, O(I), St && (Rt.attachShadow = function() {
                            return O(St.apply(this, arguments))
                        })) : (T = [], I[P]("DOMNodeInserted", u(F)), I[P]("DOMNodeRemoved", u(D))), I[P](X, c), I[P]("readystatechange", c), Rt.cloneNode = function(t) {
                            var e = Dt.call(this, !!t),
                                n = i(e);
                            return -1 < n && C(e, rt[n]), t && r(e.querySelectorAll(ot)), e
                        }), -2 < st.call(nt, Y + s) + st.call(nt, Q + s) && d(t), !tt.test(s) || -1 < st.call(et, s)) throw new Error("The type " + t + " is invalid");
                    var s, a, l = function() {
                            return h ? I.createElement(p, s) : I.createElement(p)
                        },
                        f = e || at,
                        h = ut.call(f, V),
                        p = h ? e[V].toUpperCase() : s;
                    return h && -1 < st.call(nt, Q + p) && d(p), a = nt.push((h ? Y : Q) + s) - 1, ot = ot.concat(ot.length ? "," : "", h ? p + '[is="' + t.toLowerCase() + '"]' : p), l.prototype = rt[a] = ut.call(f, "prototype") ? f.prototype : Ct(Rt), n(I.querySelectorAll(ot), F), l
                }, I.createElement = zt = function(t, e) {
                    var n = g(e),
                        r = n ? Wt.call(I, t, xt(n)) : Wt.call(I, t),
                        o = "" + t,
                        i = st.call(nt, (n ? Y : Q) + (n || o).toUpperCase()),
                        a = -1 < i;
                    return n && (r.setAttribute("is", n = n.toLowerCase()), a && (a = s(o.toUpperCase(), n))), Qt = !I.createElement.innerHTMLHelper, a && C(r, rt[i]), r
                }), m.prototype = {
                    constructor: m,
                    define: Et ? function(t, e, n) {
                        if (n) v(t, e, n);
                        else {
                            var r = t.toUpperCase();
                            wt[r] = {
                                constructor: e,
                                create: [r]
                            }, Ot.set(e, r), yt.define(t, e)
                        }
                    } : v,
                    get: Et ? function(t) {
                        return yt.get(t) || b(t)
                    } : b,
                    whenDefined: Et ? function(t) {
                        return Mt.race([yt.whenDefined(t), E(t)])
                    } : E
                }, yt) try {
                ! function(e, n, r) {
                    if (n[V] = "a", e.prototype = Ct(HTMLAnchorElement.prototype), e.prototype.constructor = e, t.customElements.define(r, e, n), jt.call(I.createElement("a", {
                            is: r
                        }), "is") !== r || Et && jt.call(new e, "is") !== r) throw n
                }(function t() {
                    return Reflect.construct(HTMLAnchorElement, [], t)
                }, {}, "document-register-element-a")
            } catch (t) {
                _()
            } else _();
            try {
                Wt.call(I, "a", "a")
            } catch (t) {
                xt = function(t) {
                    return {
                        is: t
                    }
                }
            }
        }
        n(e), t.exports = n
    }).call(e, function() {
        return this
    }())
}, function(t, e) {
    "use strict";

    function n() {
        return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function(t) {
            var e = 16 * Math.random() | 0,
                n = "x" === t ? e : 3 & e | 8;
            return n.toString(16)
        })
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    }), e.createUuid = n
}]);