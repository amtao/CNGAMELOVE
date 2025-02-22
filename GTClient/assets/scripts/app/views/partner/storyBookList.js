var i = require("Utils");
var p = require("Initializer");
var t = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        booklist:t,
    },

    ctor() {
        this._curHero = null;
    },

    onLoad() {
        //this._curHero = this.node.openParam;
        var heroid = this.node.openParam.id;
        this._curHero = p.servantProxy.getHeroData(heroid)
        if (this._curHero){
            var e = localcache.getItem(localdb.table_heroinfo, this._curHero.id);
            for (var o = [], m = p.jibanProxy.getHeroJbLv(this._curHero.id).level % 1e3, n = 1; n < 11; n++) {
                var r = {};
                r.jb = n;
                r.heroId = this._curHero.id;
                r.active = m >= n;
                o.push(r);
            }
            this.booklist.data = o;
        }
    },

   

    onClose() {
        i.utils.closeView(this);
    },



    // update (dt) {},
});
