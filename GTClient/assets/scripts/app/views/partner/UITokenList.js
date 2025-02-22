var i = require("Utils");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("UrlLoad");
var s = require("formula");
var t = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        lblxiaoguo:cc.Label,
        lblzhenglue:cc.Label,
        lblzhimou:cc.Label,
        lblmeili:cc.Label,
        listview:t
    },

    ctor() {
        this._curHero = null;
    },

    onLoad() {
        var heroid = this.node.openParam.id;
        this._curHero = n.servantProxy.getHeroData(heroid)
        var ls= n.servantProxy.getXinWuItemListByHeroid(this._curHero.id);
        if (ls == null){
            i.alertUtil.alert(i18n.t("HERO_HASNOTTOKEN"));
            this.onClose();
            return;
        }  
        facade.subscribe("SERVANT_TOKEN_UPDATE", this.onUpdateToken, this);
        //this._curHero = this.node.openParam;
        this.onUpdateToken();
    },


    onUpdateToken(){
        var ls= n.servantProxy.getXinWuItemListByHeroid(this._curHero.id);
        var a =n.servantProxy.getTokensInfo(this._curHero.id);
        var _t = [0,0,0,0];
        if (a != null){
            for (let k in a){
                var _m = a[k];
                for (var ll = 1;ll < 5;ll++){
                    _t[ll-1] += _m.prop[String(ll)] != null ?_m.prop[String(ll)]: 0;
                }
            }
        }
        this.lblxiaoguo.string = "" + _t[0];
        this.lblzhenglue.string = "" + _t[2];
        this.lblzhimou.string = "" + _t[1];
        this.lblmeili.string = "" + _t[3];
        var _data = [];
        for (var _s = 0;_s < ls.length;_s++){
            var _m = ls[_s];
            _data.push({cfg:_m,sdata:a != null ? a[_m.id] : null ,heroid:this._curHero.id});
        }
        //console.error("_data.length:",_data)
        this.listview.data = _data;
    },

    onClose() {
        i.utils.closeView(this);
    },



    // update (dt) {},
});
