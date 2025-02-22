let renderItem = require("RenderListItem");
let ItemSlotUI = require("ItemSlotUI");
let initializer = require("Initializer");
let scUIUtils = require("UIUtils");
let scUtils = require("Utils");

cc.Class({
    extends: renderItem,

    properties: {
        imgSlot: ItemSlotUI,
        nodeLock:cc.Node,
        nodeChoose:cc.Node,
        nodeRed:cc.Node,
    },

    showData() {
        let t = this._data;
        if (t) {
            this.nodeLock.active = false;
            this.imgSlot.node.active = false;
            this.nodeRed.active = false;
            let cData = initializer.servantProxy.collectAwardInfo;
            if (cData[String(t.cfg.id)]){
                this.imgSlot.node.active = true;
                this.imgSlot.data = {kind:400,id:t.cfg.id,count:1};
                let cfg = localcache.getFilter(localdb.table_collection_rwd,"rid",cData[String(t.cfg.id)].rwd + 1,"type",t.cfg.id);
                if (cfg != null && cfg.need <= cData[String(t.cfg.id)].num){
                    this.nodeRed.active = true;
                }
                else{
                    let maxcfg = localcache.getItem(localdb.table_max_rwd,t.cfg.id);
                    let maxScore = initializer.servantProxy.collectInfo.maxScore;
                    if (maxScore[String(t.cfg.id)] && maxScore[String(t.cfg.id)].score >= maxcfg.maxweight && maxScore[String(t.cfg.id)].pick == 0){
                        this.nodeRed.active = true;
                    }
                }
            }
            else{
                this.nodeLock.active = true;
            }
            this.setSelect(t.isChoose);
        }
    },

    onClickItem() {
        facade.send("CHOOSE_FENGWUZHI_ITEM",{id : this.data.cfg.id})
    },

    setSelect(flag){
        this.nodeChoose.active = flag;
    },
});
