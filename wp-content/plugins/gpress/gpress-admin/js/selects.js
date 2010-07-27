/*
Stylish Select 0.4.1 - $ plugin to replace a select drop down box with a stylable unordered list
http://scottdarby.com/

Requires: jQuery 1.3 or newer

Contributions from Justin Beasley: http://www.harvest.org/ & Anatoly Ressin: http://www.artazor.lv/

Dual licensed under the MIT and GPL licenses.

*/
(function(a) {
    a("html").addClass("stylish-select");
    Array.prototype.indexOf = function(c, d) {
        for (var b = (d || 0); b < this.length; b++) {
            if (this[b] == c) {
                return b
            }
        }
    };
    a.fn.extend({
        getSetSSValue: function(b) {
            if (b) {
                a(this).val(b).change();
                return this
            } else {
                return a(this).find(":selected").val()
            }
        },
        resetSS: function() {
            var b = a(this).data("ssOpts");
            $this = a(this);
            $this.next().remove();
            $this.unbind().sSelect(b)
        }
    });
    a.fn.sSelect = function(b) {
        return this.each(function() {
            var i = {
                defaultText: "Please select",
                animationSpeed: 0,
                ddMaxHeight: ""
            };
            var l = a.extend(i, b),
            e = a(this),
            j = a('<div class="selectedTxt"></div>'),
            r = a('<div class="newListSelected" tabindex="0"></div>'),
            z = a('<ul class="newList"></ul>'),
            t = -1,
            d = -1,
            m = [],
            w = false,
            v = false,
            x;
			
			// Remove existing one
			if(jQuery(this).next().hasClass("newListSelected")){
				jQuery(this).next().remove();
			}
            a(this).data("ssOpts", b);
            r.insertAfter(e);
            j.prependTo(r);
            z.appendTo(r);
            e.hide();
            if (e.children("optgroup").length == 0) {
                e.children().each(function(B) {
                    var C = a(this).text();
                    var A = a(this).val();
                    m.push(C.charAt(0).toLowerCase());
                    if (a(this).attr("selected") == true) {
                        l.defaultText = C;
                        d = B
                    }
                    z.append(a('<li><a href="JavaScript:void(0);">' + C + "</a></li>").data("key", A))
                });
                x = z.children().children()
            } else {
                e.children("optgroup").each(function() {
                    var A = a(this).attr("label"),
                    C = a('<li class="newListOptionTitle">' + A + "</li>");
                    C.appendTo(z);
                    var B = a("<ul></ul>");
                    B.appendTo(C);
                    a(this).children().each(function() {++t;
                        var E = a(this).text();
                        var D = a(this).val();
                        m.push(E.charAt(0).toLowerCase());
                        if (a(this).attr("selected") == true) {
                            l.defaultText = E;
                            d = t
                        }
                        B.append(a('<li><a href="JavaScript:void(0);">' + E + "</a></li>").data("key", D))
                    })
                });
                x = z.find("ul li a")
            }
            var o = z.height(),
            n = r.height(),
            y = x.length;
            if (d != -1) {
                h(d, true)
            } else {
                j.text(l.defaultText)
            }
            function p() {
                var B = r.offset().top,
                A = jQuery(window).height(),
                C = jQuery(window).scrollTop();
                if (o > parseInt(l.ddMaxHeight)) {
                    o = parseInt(l.ddMaxHeight)
                }
                B = B - C;
                if (B + o >= A) {
                    z.css({
                        top: "-" + o + "px"/*,
                        height: o*/
                    });
                    e.onTop = true
                } else {
                    z.css({
                        top: n + "px"/*,
                        height: o*/
                    });
                    e.onTop = false
                }
            }
            p();
            a(window).resize(function() {
                p()
            });
            a(window).scroll(function() {
                p()
            });
            function s() {
                r.css("position", "relative")
            }
            function c() {
                r.css("position", "static")
            }
            j.click(function(A) {
                A.stopPropagation();
                a(".newList").not(a(this).next()).hide().parent().removeClass("newListSelFocus");
                z.toggle();
                s();
                //x.eq(d).focus()
            });
            x.click(function(B) {
                var A = a(B.target);
                d = x.index(A);
                v = true;
                h(d);
                z.hide();
                r.css("position", "static")
            });
            x.hover(function(B) {
                var A = a(B.target);
                A.addClass("newListHover")
            },
            function(B) {
                var A = a(B.target);
                A.removeClass("newListHover")
            });
            function h(A, D) {
                x.removeClass("hiLite").eq(A).addClass("hiLite");
                if (z.is(":visible")) {
                    //x.eq(A).focus()
                }
                var C = x.eq(A).text();
                var B = x.eq(A).parent().data("key");
                if (D == true) {
                    e.val(B);
                    j.text(C);
                    return false
                }
                e.val(B);
				j.text(C);
            }
            e.change(function(A) {
                $targetInput = a(A.target);
                if (v == true) {
                    v = false;
                    return false
                }
                $currentOpt = $targetInput.find(":selected");
                d = $targetInput.find("option").index($currentOpt);
                h(d, true);
            });
            function q(A) {
                A.onkeydown = function(D) {
                    var C;
                    if (D == null) {
                        C = event.keyCode
                    } else {
                        C = D.which
                    }
                    v = true;
                    switch (C) {
                    case 40:
                    case 39:
                        u();
                        return false;
                        break;
                    case 38:
                    case 37:
                        k();
                        return false;
                        break;
                    case 33:
                    case 36:
                        g();
                        return false;
                        break;
                    case 34:
                    case 35:
                        f();
                        return false;
                        break;
                    case 13:
                    case 27:
                        z.hide();
                        c();
                        return false;
                        break
                    }
                    keyPressed = String.fromCharCode(C).toLowerCase();
                    var B = m.indexOf(keyPressed);
                    if (typeof B != "undefined") {++d;
                        d = m.indexOf(keyPressed, d);
                        if (d == -1 || d == null || w != keyPressed) {
                            d = m.indexOf(keyPressed)
                        }
                        h(d);
                        w = keyPressed;
                        return false
                    }
                }
            }
            function u() {
                if (d < (y - 1)) {++d;
                    h(d)
                }
            }
            function k() {
                if (d > 0) {--d;
                    h(d)
                }
            }
            function g() {
                d = 0;
                h(d)
            }
            function f() {
                d = y - 1;
                h(d)
            }
            r.click(function() {
                q(this)
            });
            r.focus(function() {
                a(this).addClass("newListSelFocus");
                q(this)
            });
            r.blur(function() {
                a(this).removeClass("newListSelFocus")
            });
            a("body").click(function() {
                r.removeClass("newListSelFocus");
                z.hide();
                c()
            });
            j.hover(function(B) {
                var A = a(B.target);
                A.parent().addClass("newListSelHover")
            },
            function(B) {
                var A = a(B.target);
                A.parent().removeClass("newListSelHover")
            });
            z.css("left", "0").hide()
        })
    }
})(jQuery);