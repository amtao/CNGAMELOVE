var i = require("Utils");
var n = require("Initializer");
var l = require("SelectMax");
var r = require("BagHeItem");
cc.Class({
    extends: cc.Component,
    properties: {
        item: r,
        silder: l,
    },
    ctor() {
        this._data = null;
    },
    onClickHeCheng() {
        if (this._data) {
            let t = this._data;
            let e = this.silder && this.silder.node.active ? this.silder.curValue: 1;
            if (0 != t.totonum && t.times < e) {
                i.alertUtil.alert(i18n.t("BAG_COMPOSE_COUNT_LIMIT"));
                return;
            }
            for (var o = 0; o < t.need.length; o++) {
                var cg = t.need[o];
                if (n.bagProxy.getItemCount(cg.id) < cg.count * e) {
                    i.alertUtil.alertItemLimit(cg.id);
                    return;
                }
            }
            console.error("e:",e)
            n.bagProxy.sendCompose(t.itemid, e);
            this.onClickClost();
        }
    },
    onClickClost() {
        i.utils.closeView(this);
    },
    onClickOther(t) {
        this._data = t;
        this.updateShow();
    },
    updateShow() {
        var t = this._data;
        if (t) {
            this.item.data = t;
            var e = t.need[0],
            o = n.bagProxy.getItemCount(e.id),
            i = 99;
            i = Math.floor(o / e.count) < i ? Math.floor(o / e.count) : i;
            0 != t.totonum && (i = t.times < i ? t.times: i);
            this.silder.max = i <= 0 ? 1 : i;
        }
    },
    onLoad() {
        //var t = this; 
        let param = this.node.openParam;
        this._data = param;
        if(null != param) {
            let count = 0;
            for(let i in param) {
                count ++;
            }
            if(count == 1) {
                this._data = n.bagProxy.getHecheng(param.id);
            }
        }
        
        this.updateShow();
        //var e = this;
        // this.silder.changeHandler = function() {
        //     e.item.nodeGold && e.item.nodeGold.active && (e.item.lblGold.string = e._data.need[1].count * t.silder.curValue + "");
        // };
        facade.subscribe("BAG_CLICK_BLANK", this.onClickClost, this);
        facade.subscribe("BAG_CLICK_HECHENG", this.onClickOther, this);
        facade.subscribe("BAG_CLICK_USE", this.onClickClost, this);
    },
});
