var i = require("RenderListItem");
var l = require("Initializer");
var r = require("UIUtils");
var s = require("List");
var u = require("UrlLoad");
var p = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblpropname1: cc.Label,
        lblpropname2: cc.Label,
        lblpropnum1:cc.Label,
        lblpropnum2:cc.Label,
        lblname:cc.Label,
        lbllevel:cc.Label,
        lblnum:cc.Label,
        img_icon:u,
        node_unlock:cc.Button,
        btn_detail:cc.Button,
        btn_condition:cc.Button,
        btn_levelup:cc.Button,
        iconBg:u,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var cg = t.cfg;
            var info = t.sdata;
            var st = info == null || 1 != info.isActivation;
            var lv = 1;
            if (st){
                if(info && info.count > 0) {
                    this.lblnum.string = "";
                    this.node_unlock.getComponent(cc.Sprite).spriteFrame = this.btn_levelup.getComponent(cc.Sprite).spriteFrame;
                } else { 
                    this.lblnum.string = "0";
                }
                this.lbllevel.string = "";
            }
            else{
                this.lbllevel.string = `LV${info.lv}`;
                this.lblnum.string = `${info.count}`;
                lv = info.lv;
            }            

            this.lblnum.string = "";
            //this.btn_condition.node.active = st;
            this.btn_detail.node.active = !st;
            //this.btn_levelup.node.active = !st;
            this.node_unlock.node.active = st;
            this.lblname.string = cg.name;
            var rad = 1;
            if (lv > 1){
                for (var kk = 2; kk <= lv;kk++ ){
                    var _mm = localcache.getItem(localdb.table_tokenlvup,kk);
                    if (_mm == null){
                        console.error("kk:",kk);
                    }
                    else
                        rad *= (1+_mm.attri/100);
                }            
            }
            var proplist = cg.type[2];
            if (proplist.length == 2){
                this.lblpropname1.string = l.servantProxy.getPropName(proplist[0].prop);
                this.lblpropnum1.string = Math.ceil(proplist[0].value * rad);
                this.lblpropname2.string = l.servantProxy.getPropName(proplist[1].prop);
                this.lblpropnum2.string = Math.ceil(proplist[1].value * rad);
            }
            else{
                this.lblpropname1.string = l.servantProxy.getPropName(proplist[0].prop);
                this.lblpropnum1.string = Math.ceil(proplist[0].value * rad);
                this.lblpropnum2.string = "";
                this.lblpropname2.string = "";
            }
            this.img_icon.url = r.uiHelps.getItemSlot(cg.icon);
            var frame = cg.color < 2 ? 2 : cg.color;
            this.iconBg.url = r.uiHelps.getItemColor(frame);
            let nextTokenLevelUpCfg = localcache.getItem(localdb.table_tokenlvup,lv + 1);
            this.btn_levelup.node.active = (!st && nextTokenLevelUpCfg != null);
        }
    },

    onClickLevelUp(){
        //console.error("onClickLevelUp")
        p.utils.openPrefabView("partner/UseTokenView",null,this._data);
    },

    onClickDetail(){
        //console.error("onClickDetail")
        p.utils.openPrefabView("partner/TokenDetailView",null,this._data);
    },

    onClickConditionActive(){
        //console.error("onClickConditionActive")
        p.utils.openPrefabView("partner/TokenUnActiveView",null,this._data);
    },

    onClickActive(){
        var t = this._data;
        var info = t.sdata;
        if (info == null || info.count <= 0){
            p.alertUtil.alert(i18n.t("TOKEN_CANNOTACTIVE"));
            return;
        }
        l.servantProxy.sendTokenActivation(t.heroid,t.cfg.id);
    }

});
