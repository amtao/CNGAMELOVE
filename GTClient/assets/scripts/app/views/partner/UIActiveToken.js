var l = require("Initializer");
var r = require("UIUtils");
var s = require("List");
var u = require("UrlLoad");
var i = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblname: cc.Label,
        img_icon:u,
        lbleffect:cc.Label,
        btn_active:cc.Button,
        lblcontent:cc.Label
    },
    ctor() {},

    onLoad() {
        var t = this.node.openParam;
        if (t){
            var cg = t.cfg;
            var info = t.sdata;
            this.lblname.string = cg.name;
            this.lblcontent.string = cg.explain;
            var proplist = cg.type[2];
            var str = ""
            for (let kk of proplist){
                str += l.servantProxy.getPropName(kk.prop) + kk.value + "   "
            }
            this.lbleffect.string = str;
            if (info != null && info.isActivation == 1){
                this.btn_active.interactable = false;
            }
            this.img_icon.url = r.uiHelps.getItemSlot(cg.icon);
        }
        
    },

    onClose(){
        i.utils.closeView(this);
    },

    onClickActive(){
        var t = this.node.openParam;
        var info = t.sdata;
        if (info == null){
            i.alertUtil.alert(i18n.t("TOKEN_CANNOTACTIVE"));
            return;
        }
        l.servantProxy.sendTokenActivation(t.heroid,t.cfg.id);
        this.onClose();
    }


});
