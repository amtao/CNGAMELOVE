var i = require("Utils");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        tablentList: n,
        widget: cc.Node,
    },
    ctor() {
        this._curHero = null;
        this._curIndex = -1;
        this._orgY = 0;
        this._orgH = 0;
    },
    onLoad() {
        facade.subscribe("SERVANT_UP", this.updateServant, this);
        this.updateShow();
    },
    updateServant() {
        this._curHero = l.servantProxy.getHeroData(this._curHero.id);
        this.updateShow();
    },
    updateShow(t) {
        void 0 === t && (t = null);
        this._curHero = t || this._curHero;
        this._curIndex = -1;
        this._curHero && this.onClickTab(null, 0);
        this.tablentList.data = this._curHero.epskill;
    },
    onClickItem(t, e) {
        e.data && i.utils.openPrefabView("servant/BookUpLv", null, e.data);
    },
    onClickTab(t, e) {
        var o = parseInt(e) - 1;
        o == this._curIndex ? (this._curIndex = -1) : (this._curIndex = o);
        for (var i = [], n = 0; n < this._curHero.epskill.length; n++) {
            var l = this._curHero.epskill[n],
            r = localcache.getItem(localdb.table_epSkill, l.id + ""); ! r || (r.ep != this._curIndex + 1 && -1 != this._curIndex) || i.push(l);
        }
    },
});
