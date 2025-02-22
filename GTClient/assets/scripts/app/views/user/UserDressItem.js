var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("ShaderUtils");
var r = require("Initializer");
var a = require("UIUtils");
let BagProxy = require("BagProxy");
cc.Class({
    extends: i,
    properties: {
        select:{
            set: function(t) {
                var e = this._data;
                this.nodeSelect.active = t && (null == e || 0 != e.id);
            },
            enumerable: !0,
            configurable: !0
        },
        nodeLock:cc.Node,
        nodeGold:cc.Node,
        lblGold:cc.Label,
        url:n,
        lblName:cc.Label,
        //img:cc.Sprite,
        img2:cc.Sprite,
        btn:cc.Button,
        itemUrl:n,
        nodeGoldImg:cc.Node,
        nodeSelect:cc.Node,
        propimg:n,
        lblProp:cc.Label,
        lblOut:cc.Label,
        nodeProp:cc.Node,
        nodeRemove:cc.Node,
        propInfo:cc.Label,
    },

    onLoad() {
        this.addBtnEvent(this.btn);
    },
    resetPropNodePos(itemInfo){
        // if(this.propInfo){
        //     this.nodeProp.setPosition(0,-26);
        //     this.propInfo.node.width = 45;
        //     if (1 != itemInfo.prop_type){
        //         this.propInfo.node.width = 110;
        //         this.nodeProp.setPosition(0,-20);
        //     }
        // }
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = r.playerProxy.isUnlockCloth(t.id);
            //l.shaderUtils.setImageGray(this.img, !e);
            l.shaderUtils.setImageGray(this.img2, !e);
            this.lblName.string = t.name;
            this.nodeRemove && (this.nodeRemove.active = !1);
            this.lblOut && (this.lblOut.string = t.text);
            if (0 == t.id) {
                this.url.url = "";
                this.nodeRemove && (this.nodeRemove.active = !0);
                this.itemUrl && (this.itemUrl.node.active = !1);
                this.nodeProp.active = this.nodeLock.active = this.nodeGoldImg.active = this.nodeGold.active = !1;
                return;
            }
            this.nodeProp.active = t.prop && t.prop.length > 0;
            if (t.prop && t.prop.length > 0){
                if (1 == t.prop_type) {
                    this.lblProp.string = "+" + t.prop[0].value;
                    this.propimg.url = a.uiHelps.getUserclothePic("prop_" + t.prop[0].prop);
                } else {
                    this.propimg.url = a.uiHelps.getClotheProImg(t.prop_type, t.prop[0].prop);
                    let epData = r.playerProxy.getUserEpData(t.prop_type);
                    let addPerInfo = Math.floor(t.prop[0].value / 100);
                    let propEpData = r.playerProxy.getPropDataByIndex(t.prop[0].prop,epData,addPerInfo*0.01);
                    this.lblProp.string = "+" + addPerInfo + "%" +"("+propEpData+")";
                }
            }
            //this.resetPropNodePos(t);
            if (t.money){
                var o = t.money.itemid;
                this.lblOut && (this.lblOut.node.active = null == o);
                this.nodeGold.active = !e && 2 == t.unlock;
                this.nodeLock.active = !e && 1 == t.unlock;
                this.nodeGold.active = (null != this.lblOut || this.nodeGold.active);
                this.lblGold.string = t.money.count ? t.money.count + "": "";
                this.itemUrl && (this.itemUrl.node.active = o && 1 != o);
                this.nodeGoldImg.active = 1 == o;
                this.itemUrl && this.itemUrl.node.active && (this.itemUrl.url = a.uiHelps.getItemSlot(o));
            }
            else{
                this.nodeLock.active = !e;
            }



            if (t.model){
                var i = t.model.split("|");
                this.url.url = a.uiHelps.getRolePart(i[0]);
            }

            if (t.icon){
                this.url.url = a.uiHelps.getRolePart(t.icon);
            }
            
            if(t.goldLeafCost){
                this.nodeGold.active = true;
                this.nodeGoldImg.active = true;
                this.lblGold.string ='' + t.goldLeafCost;
            }
            if(this.nodeGold.active){
                this.nodeGold.active = !(o == null || o == undefined);
            }
        }
    },

    selectHandle: function(select) {
        this.nodeSelect.active = select;
    }
});
