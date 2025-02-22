var l = require("Initializer");
var r = require("UIUtils");
var s = require("List");
var u = require("UrlLoad");
var i = require("Utils");
let ItemSlotUI = require("ItemSlotUI");
cc.Class({
    extends: cc.Component,
    properties: {
        btn_levelup:cc.Button,
        item:ItemSlotUI,
        curPropIconArr:[u],
        curPropNumArr:[cc.Label],
        nextPropIconArr:[u],
        nextPropNumArr:[cc.Label],
    },

    ctor() {

    },

    onLoad() {
        facade.subscribe("SERVANT_TOKEN_UPDATE", this.onUpdateToken, this);
        var t = this.node.openParam;
        var cg = t.cfg;
        this.onUpdateToken();
    },

    onUpdateToken(){
        var t = this.node.openParam;
        var cg = t.cfg;
        var a =l.servantProxy.getTokensInfo(t.heroid);
        var info = a[t.cfg.id];
        var lv = info.lv;
        var rad = 1;
        var nrad = 1;
        if (lv >= 1){
            for (var kk = 2; kk <= lv+1;kk++ ){
                var _mm = localcache.getItem(localdb.table_tokenlvup,kk);
                if (_mm == null){
                    break;
                }
                if (kk <= lv){
                    rad *= (1+_mm.attri/100);
                }
                nrad *= (1+_mm.attri/100);
            }            
        }
        var proplist = cg.type[2];
        for (let ii = 0; ii < 2;ii++){
            if (proplist[ii] == null){
                this.curPropIconArr[ii].url = "";
                this.curPropNumArr[ii].string = "";
                this.nextPropIconArr[ii].url = "";
                this.nextPropNumArr[ii].string = "";
            }
            else{
                this.curPropIconArr[ii].url = r.uiHelps.getPinzhiPicNew(proplist[ii].prop);
                this.curPropNumArr[ii].string = Math.ceil(proplist[ii].value * rad);
                this.nextPropIconArr[ii].url = r.uiHelps.getPinzhiPicNew(proplist[ii].prop);
                this.nextPropNumArr[ii].string = Math.ceil(proplist[ii].value * nrad);
            }       
        }
        var nlc = localcache.getItem(localdb.table_tokenlvup,lv+1);
        if (nlc == null){
            this.item.data = {id:t.cfg.id,kind:200,count:1}
            this.btn_levelup.interactable = false;
        }
        else{
            this.btn_levelup.interactable = true;
            this.item.data = {id:t.cfg.id,kind:200,count:100,showStr:`${info.count}/${nlc.cost}`}
            if (nlc.cost > info.count){
                this.btn_levelup.interactable = false;
            }
        }
    },

    onClose() {
        i.utils.closeView(this);
    },

    onLevelUp(){
        var t = this.node.openParam;
        l.servantProxy.sendTokenUpLv(t.heroid,t.cfg.id);
    },

});
