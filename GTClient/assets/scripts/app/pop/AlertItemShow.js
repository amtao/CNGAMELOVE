var i = require("ItemSlotUI");
var n = require("Utils");
var l = require("BagProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        itemSlot: null,
        lblDes: cc.Label,
        nodeUse: cc.Node,
    },
    ctor() {
        this._curData = null;
    },
    onLoad() {
        facade.subscribe("CLOST_ITEM_SHOW", this.onClickClost, this);
        var t = this.node.openParam;
        let bAutoClose = true;
        if (null != t) {
            this._curData = t;
            this.itemSlot.data = t;
            var e = t.id ? t.id: t.itemid,
            o = t.kind ? t.kind: 1;
            this.nodeUse.active = !1;
            switch (o) {
            case l.DataType.HEAD_BLANK:
                var i = localcache.getItem(localdb.table_userblank, e);
                this.lblDes.string = i.des;
                break;
            case l.DataType.CLOTHE:
                var n = localcache.getItem(localdb.table_userClothe, e);
                this.lblDes.string = n.des;
                break;
            case l.DataType.JB_ITEM:
                var r = localcache.getItem(localdb.table_heropve, t.id);
                this.lblDes.string = r.msg;
                break;
            case l.DataType.CHENGHAO:
                var a = localcache.getItem(localdb.table_fashion, t.id);
                this.lblDes.string = a ? a.des: "";
                break;
            case l.DataType.HERO_DRESS:{
                let heroDresssArray = localcache.getFilters(localdb.table_heroDress, "id", t.id);
                if(heroDresssArray && heroDresssArray.length > 0){
                    this.lblDes.string = heroDresssArray[0].des;
                }
            }break;
            case l.DataType.HOMEPART_F_205:{
                var r = localcache.getItem(localdb.table_furniture, t.id)
                this.lblDes.string = r.desc;
            }break;
            case l.DataType.BUSINESS_ITEM:{
                let cfg = localcache.getItem(localdb.table_wupin, t.id);
                this.lblDes.string = cfg.desc;
                this.nodeUse.active = false
            }break;
            default:
                var s = localcache.getItem(localdb.table_item, e),
                c = s ? s.explain.split("|") : [];
                s.explain.split("|");
                this.lblDes.string = c.length > 1 ? c[1] : s ? s.explain: i18n.t("COMMON_NULL");
                this.nodeUse.active = s.type && "item" == s.type[0];
                bAutoClose = !this.nodeUse.active;
            }
            var _ = this.lblDes.node.getContentSize().height + 10;
            // this.nodeBg && (this.nodeBg.parent.height = this.nodeBg.height = _ < 126 ? 126 : _);
            // this.nodeBg && this.maskBG && (this.maskBG.height = this.nodeBg.height);
        }
        if(bAutoClose) {
            let self = this;
            this.scheduleOnce(() => {
                if(null != self.node) {
                    self.onClickClost();
                }
            }, 2);
        }        
    },

    onClickUse() {
        n.utils.openPrefabView("bag/BagUse", !1, this._curData);
        this.onClickClost();
    },

    onClickClost() {
        n.utils.closeView(this);
        n.utils.popNext(!1);
    },
});
