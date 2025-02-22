var i = require("RenderListItem");
var n = require("Utils");
var l = require("Initializer");
var r = require("ItemSlotUI");
var a = require("UIUtils");
var s = require("SelectMax");
var ShaderUtils = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        //lblName: cc.Label,
        lblCount: cc.Label,
        //lblInfo: cc.Label,
        silderCount: s,
        itemSlot: r,
        lbldes:cc.Label,
        btnSp:cc.Sprite,
    },
    ctor() {},
    showData() {
        var t = localcache.getItem(localdb.table_item, this._data.id);
        if (t) {
            this.itemId = parseInt(this._data.id);
            //this.lblInfo.string = t.explain;
            //this.lblName.string = t.name;
            let myCount = l.bagProxy.getItemCount(t.id);
            this.lblCount.string = myCount;
            //o = n.utils.getParamInt("show_slider_count");
            this.silderCount.max = myCount;
            //this.silderCount.node.active = e >= o;
            this.lbldes.string = t.explain.split("，")[1];
            this.silderCount.min = 0;
            this.silderCount.showMmin = 0;
            if (myCount > 0) {
                this.silderCount.curValue = 1;
            } else {
                this.silderCount.curValue = 0;
            }
            var i = new a.ItemSlotData();
            i.id = this.itemId;
            this.itemSlot.data = i;
            this.silderCount.changeHandler = ()=>{
                ShaderUtils.shaderUtils.setImageGray(this.btnSp,this.silderCount.curValue == 0);
            }
            ShaderUtils.shaderUtils.setImageGray(this.btnSp,this.silderCount.curValue == 0);
        }
    },
    onClickUse(t, e) {     
        if (this.silderCount._curValue <= 0){
            n.alertUtil.alert18n("PARYNER_ROOMTIPS34");
            //n.alertUtil.alertItemLimit(this._data.id);
        } 
        else {
           l.servantProxy.sendHeroGift(this.data.heroid, [{ gid: this.data.id, num: this.silderCount._curValue }]);
        }
    },
});
