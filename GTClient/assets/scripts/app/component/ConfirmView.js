var i = require("Utils");
var n = require("ItemSlotUI");
var l = require("UIUtils");
var r = require("SelectMax");
var timeProxy = require("TimeProxy");
var Initializer = require("Initializer");

var ConfirmView = cc.Class({
    extends: cc.Component,

    properties: {
        textLabel: cc.RichText,
        lblLeft: cc.Label,
        lblRight: cc.Label,
        itemSlot: n,
        silder: r,
        edit: cc.EditBox,
        toggle: cc.Toggle,
        lblcount: cc.Label,
        nBtnClose: cc.Node,
        nBtnBg: cc.Node,
    },

    ctor() {},

    onLoad() {
        this.edit && (this.edit.placeholder = i18n.t("COMMON_INPUT_TXT"));
        var t = this.node.openParam;
        if (t) {
            this.textLabel.string = t.txt;
            var e = t.skip;
            this.toggle && (this.toggle.node.active = !i.stringUtil.isBlank(e));
            if (!i.stringUtil.isBlank(e) && ConfirmView.checks[e]) {
                this.node.active = !1;
                this.onClickOK(null, 1);
                return;
            }
            this.toggle && (this.toggle.isChecked = ConfirmView.checks[e]);
            t.color && (this.textLabel.node.color = t.color);
            if (this.itemSlot) {
                var n = new l.ItemSlotData();
                n.id = t.itemId;
                n.count = t.count;
                this.itemSlot.data = n;
                if (this.silder) {
                    this.silder.baseCount = t.baseCount;
                    this.silder.node.active && (t.baseCount && 0 != t.baseCount ? (this.silder.max = Math.floor(t.count / t.baseCount)) : (this.silder.max = t.count));
                }
            }
            this.edit && (this.edit.placeholder = t.txt);
            this.lblcount && (this.lblcount.string = this.lblcount.string);
            this.lblLeft && (this.lblLeft.string = t.left ? t.left: i18n.t("COMMON_YES"));
            this.lblRight && (this.lblRight.string = t.right ? t.right: i18n.t("COMMON_NO"));
            if(this.nBtnClose) {
                let bShow = null != t.close;
                this.nBtnClose.active = bShow;
                if(this.nBtnBg && bShow) {
                    this.nBtnBg.on('click', this.onClickClose, this);
                }
            }
        }
    },

    onClickOK(t, e) {
        void 0 === e && (e = null);
        var n = this.node.openParam;
        if (n && n.handler) {
            if (this.toggle) {
                var l = n.skip;
                i.stringUtil.isBlank(l) || null != e || (ConfirmView.checks[l] = this.toggle.isChecked);
            }
            n.target ? this.silder ? n.handler.apply(n.target, [this.silder.node.active ? this.silder.curValue: 1]) : this.edit ? n.handler.apply(n.target, [this.edit.string]) : n.handler.apply(n.target) : this.silder ? n.handler(this.silder.node.active ? this.silder.curValue: 1) : this.edit ? n.handler(this.edit.string) : n.handler();
        }
        i.utils.closeView(this);
    },

    onClickCancel() {
        var t = this.node.openParam;
        if(t.cancel) {
            console.log("cancel cancel cancel");
            t.cancel();
            i.utils.closeView(this);
            return;
        }
        t && t.right && !i.stringUtil.isBlank(t.right) && t && t.handler && (t.target ? t.handler.apply(t.target, ConfirmView.NO) : t.handler(ConfirmView.NO));
        if(t && t.lost) {
            this.scheduleOnce(function(){
                let viewNameStr = "seriesFirstCharge/seriesFirstCharge";
                let isFRecharge = Initializer.seriesFirstChargeProxy.checkIsAllGot();
                !isFRecharge && timeProxy.funUtils.isCanOpenViewUrl(viewNameStr) && timeProxy.funUtils.openViewUrl(viewNameStr);
                i.utils.closeView(this);
            }, 0.2);
            return;
        }
        i.utils.closeView(this);
    },

    onClickClose() {
        let param = this.node.openParam;
        if(param.close) {
            param.close();
            i.utils.closeView(this);
        } else {
            this.onClickCancel();
        }
    },
});

ConfirmView.checks = {};
ConfirmView.NO = "NO";
ConfirmView.isSkip = function(t) {
    if (!i.stringUtil.isBlank(t.skip) && ConfirmView.checks[t.skip]) {
        t.target ? t.handler.apply(t.target) : t.handler();
        return ! 0;
    }
    return ! 1;
};