var i = require("RenderListItem");
var u = require("Utils");
var s = require("List");
cc.Class({
    extends: i,
    properties: {
        lbltitle: cc.Label,
        lblxiaoguo: cc.Label,
        lbljiban:cc.Label,
        btn_unlock:cc.Sprite,
        node_condition:cc.Node,
        btn_item:cc.Button
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_heroinfo, t.heroId);
            var o = i18n.t("COMMON_HANZI").split("|");
            this.lbltitle.string = i18n.t("SERVANT_ZHUAN_JI") + o[parseInt(t.jb) - 1];
            if (!t.active){
                this.lbljiban.string = i18n.t("COMMON_JI",{
                    value:t.jb
                })
                this.btn_unlock.node.active = false;
                this.node_condition.active = true;
                this.btn_item.interactable = false;
            }
            else{
                this.btn_unlock.node.active = true;
                this.node_condition.active = false;
                this.btn_item.interactable = true;
            }
            var i = 1e3 * localcache.getItem(localdb.table_hero, t.heroId).star + t.jb,
            l = localcache.getItem(localdb.table_yoke, i);
            this.lblxiaoguo.string = i18n.t("SERVANT_SHU_XING") + "+"+ l.prop / 100 + "%";
            
        }
    },
    onClickEnter() {
        var t = this._data
        if (t == null || !t.active) return;
        var e = localcache.getItem(localdb.table_heroinfo, t.heroId);
        var ss =  e["yoke" + t.jb];
        u.utils.openPrefabView("partner/CommonTipView",null,{
            text:ss
        });
    },

});
