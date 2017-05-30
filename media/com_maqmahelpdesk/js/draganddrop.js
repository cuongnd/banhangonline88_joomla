(function (a) {
    $jMaQma.tableDnD = {currentTable:null, dragObject:null, mouseOffset:null, oldY:0, build:function (a) {
        this.each(function () {
            this.tableDnDConfig = $jMaQma.extend({onDragStyle:null, onDropStyle:null, onDragClass:"tDnD_whileDrag", onDrop:null, onDragStart:null, scrollAmount:5, serializeRegexp:/[^\-]*$/, serializeParamName:null, dragHandle:null}, a || {});
            $jMaQma.tableDnD.makeDraggable(this)
        });
        $jMaQma(document).bind("mousemove", $jMaQma.tableDnD.mousemove).bind("mouseup", $jMaQma.tableDnD.mouseup);
        return this
    }, makeDraggable:function (a) {
        var b = a.tableDnDConfig;
        if (a.tableDnDConfig.dragHandle) {
            var c = $jMaQma("td." + a.tableDnDConfig.dragHandle, a);
            c.each(function () {
                $jMaQma(this).mousedown(function (c) {
                    $jMaQma.tableDnD.dragObject = this.parentNode;
                    $jMaQma.tableDnD.currentTable = a;
                    $jMaQma.tableDnD.mouseOffset = $jMaQma.tableDnD.getMouseOffset(this, c);
                    if (b.onDragStart) {
                        b.onDragStart(a, this)
                    }
                    return false
                })
            })
        } else {
            var d = $jMaQma("tr", a);
            d.each(function () {
                var c = $jMaQma(this);
                if (!c.hasClass("nodrag")) {
                    c.mousedown(
                        function (c) {
                            if (c.target.tagName == "TD") {
                                $jMaQma.tableDnD.dragObject = this;
                                $jMaQma.tableDnD.currentTable = a;
                                $jMaQma.tableDnD.mouseOffset = $jMaQma.tableDnD.getMouseOffset(this, c);
                                if (b.onDragStart) {
                                    b.onDragStart(a, this)
                                }
                                return false
                            }
                        }).css("cursor", "move")
                }
            })
        }
    }, updateTables:function () {
        this.each(function () {
            if (this.tableDnDConfig) {
                $jMaQma.tableDnD.makeDraggable(this)
            }
        })
    }, mouseCoords:function (a) {
        if (a.pageX || a.pageY) {
            return{x:a.pageX, y:a.pageY}
        }
        return{x:a.clientX + document.body.scrollLeft - document.body.clientLeft, y:a.clientY + document.body.scrollTop - document.body.clientTop}
    }, getMouseOffset:function (a, b) {
        b = b || window.event;
        var c = this.getPosition(a);
        var d = this.mouseCoords(b);
        return{x:d.x - c.x, y:d.y - c.y}
    }, getPosition:function (a) {
        var b = 0;
        var c = 0;
        if (a.offsetHeight == 0) {
            a = a.firstChild
        }
        while (a.offsetParent) {
            b += a.offsetLeft;
            c += a.offsetTop;
            a = a.offsetParent
        }
        b += a.offsetLeft;
        c += a.offsetTop;
        return{x:b, y:c}
    }, mousemove:function (a) {
        if ($jMaQma.tableDnD.dragObject == null) {
            return
        }
        var b = $jMaQma($jMaQma.tableDnD.dragObject);
        var c = $jMaQma.tableDnD.currentTable.tableDnDConfig;
        var d = $jMaQma.tableDnD.mouseCoords(a);
        var e = d.y - $jMaQma.tableDnD.mouseOffset.y;
        var f = window.pageYOffset;
        if (document.all) {
            if (typeof document.compatMode != "undefined" && document.compatMode != "BackCompat") {
                f = document.documentElement.scrollTop
            } else if (typeof document.body != "undefined") {
                f = document.body.scrollTop
            }
        }
        if (d.y - f < c.scrollAmount) {
            window.scrollBy(0, -c.scrollAmount)
        } else {
            var g = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight;
            if (g - (d.y - f) < c.scrollAmount) {
                window.scrollBy(0, c.scrollAmount)
            }
        }
        if (e != $jMaQma.tableDnD.oldY) {
            var h = e > $jMaQma.tableDnD.oldY;
            $jMaQma.tableDnD.oldY = e;
            if (c.onDragClass) {
                b.addClass(c.onDragClass)
            } else {
                b.css(c.onDragStyle)
            }
            var i = $jMaQma.tableDnD.findDropTargetRow(b, e);
            if (i) {
                if (h && $jMaQma.tableDnD.dragObject != i) {
                    $jMaQma.tableDnD.dragObject.parentNode.insertBefore($jMaQma.tableDnD.dragObject, i.nextSibling)
                } else if (!h && $jMaQma.tableDnD.dragObject != i) {
                    $jMaQma.tableDnD.dragObject.parentNode.insertBefore($jMaQma.tableDnD.dragObject, i)
                }
            }
        }
        return false
    }, findDropTargetRow:function (a, b) {
        var c = $jMaQma.tableDnD.currentTable.rows;
        for (var d = 0; d < c.length; d++) {
            var e = c[d];
            var f = this.getPosition(e).y;
            var g = parseInt(e.offsetHeight) / 2;
            if (e.offsetHeight == 0) {
                f = this.getPosition(e.firstChild).y;
                g = parseInt(e.firstChild.offsetHeight) / 2
            }
            if (b > f - g && b < f + g) {
                if (e == a) {
                    return null
                }
                var h = $jMaQma.tableDnD.currentTable.tableDnDConfig;
                if (h.onAllowDrop) {
                    if (h.onAllowDrop(a, e)) {
                        return e
                    } else {
                        return null
                    }
                } else {
                    var i = $jMaQma(e).hasClass("nodrop");
                    if (!i) {
                        return e
                    } else {
                        return null
                    }
                }
                return e
            }
        }
        return null
    }, mouseup:function (a) {
        if ($jMaQma.tableDnD.currentTable && $jMaQma.tableDnD.dragObject) {
            var b = $jMaQma.tableDnD.dragObject;
            var c = $jMaQma.tableDnD.currentTable.tableDnDConfig;
            if (c.onDragClass) {
                $jMaQma(b).removeClass(c.onDragClass)
            } else {
                $jMaQma(b).css(c.onDropStyle)
            }
            $jMaQma.tableDnD.dragObject = null;
            if (c.onDrop) {
                c.onDrop($jMaQma.tableDnD.currentTable, b)
            }
            $jMaQma.tableDnD.currentTable = null
        }
    }, serialize:function () {
        if ($jMaQma.tableDnD.currentTable) {
            return $jMaQma.tableDnD.serializeTable($jMaQma.tableDnD.currentTable)
        } else {
            return"Error: No Table id set, you need to set an id on your table and every row"
        }
    }, serializeTable:function (a) {
        var b = "";
        var c = a.id;
        var d = a.rows;
        for (var e = 0; e < d.length; e++) {
            if (b.length > 0)b += "&";
            var f = d[e].id;
            if (f && f && a.tableDnDConfig && a.tableDnDConfig.serializeRegexp) {
                f = f.match(a.tableDnDConfig.serializeRegexp)[0]
            }
            b += c + "[]=" + f
        }
        return b
    }, serializeTables:function () {
        var a = "";
        this.each(function () {
            a += $jMaQma.tableDnD.serializeTable(this)
        });
        return a
    }};
    a.fn.extend({tableDnD:a.tableDnD.build, tableDnDUpdate:a.tableDnD.updateTables, tableDnDSerialize:a.tableDnD.serializeTables})
})($jMaQma)