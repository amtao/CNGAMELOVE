var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");

cc.Class({

    extends: i,

    properties: {
        servant: n,
        lbName: cc.Label,
        btn: cc.Button,
    },

    ctor() {},

    onLoad() {
        this.addBtnEvent(this.btn);
    },

    showData() { //这里主要用于对战对方的显示--如果需要显示我方需要设置myHero为true
        var t = this._data;
        if (t) {
            let myHero = (null != t.myHero) ? t.myHero : false;
            if (t.isFuYue){
                this.servant.url = l.uiHelps.getWifeBody(t.id);
                let heroData = localcache.getItem(localdb.table_zuipao, t.id);
                this.lbName.string = heroData.name;
            }
            else{
                this.servant.url = l.uiHelps.getServantSpine(t.id, myHero);
                let heroData = localcache.getItem(localdb.table_hero, t.id);
                this.lbName.string = heroData.name;
            }
            
        }
    },
});
