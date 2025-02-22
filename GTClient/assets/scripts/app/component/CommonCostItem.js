
var urlload = require("UrlLoad");
var n = require("UIUtils");
var Initializer = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblcount: cc.Label,
        sp:urlload,
    },
    ctor() {
        this.m_num = 0;
        this.m_itemid = 0;
    },
    onLoad() {
        
    },

    /**
    *param num 道具需要的数量
    *param itemid 道具id
    *param prefix 数量的前缀 默认是消耗
    */
    initCostItem(num,itemid,prefix){
        this.m_prefix = prefix;
        var itemcfg = localcache.getItem(localdb.table_item,itemid);
        var url = n.uiHelps.getItemSlot(itemcfg ? itemcfg.icon: itemid)
        this.sp.url = url;
        this.updateCostItemNum(num,itemid);  
    },

    updateCostItemNum(num,itemid){
        if (itemid == null) return;
        this.m_num = num;
        this.m_itemid = itemid;
        if (this.m_prefix == null){
            this.lblcount.string = i18n.t("COMMON_XIAOHAO",{
                value:num
            })
        }
        else{
            this.lblcount.string = this.m_prefix + num;
        }   
    },

    /**获取需求的道具数量*/
    getItemNum(){
        return this.m_num;
    },

    /**获取当前的道具货币id*/
    getCurrentItemId(){
        return this.m_itemid;
    },

    /**道具是否足够*/
    isEncough(){
        let num = Initializer.bagProxy.getItemCount(this.m_itemid);
        return num >= this.m_num;
    },
});
