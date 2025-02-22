let RenderListItem = require("RenderListItem");
//let ShaderUtils = require("ShaderUtils");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
//let Initializer = require("Initializer");

cc.Class({
    extends: RenderListItem,

    properties: {
        select:{
            set: function(select) {
                this.nodeSelect.active = select;
            },
        },
        btn: cc.Button,
        lblName: cc.Label,//名字
        spBG: cc.Sprite,//背景
        spItem: cc.Sprite,//时装图标
        urlItem: UrlLoad,//时装图标
        nodeSelect: cc.Node,//选中节点
        nodeLock: cc.Node,//锁节点

        nodeGold: cc.Node,//金币节点--可以道具购买时显示
        lblGold: cc.Label,//价格
        itemUrl: UrlLoad,//兑换物品
        nodeGoldImg: cc.Node,//金币图标

        nodeProp: cc.Node,//属性节点
        propimg: UrlLoad,//属性图标
        lblProp: cc.Label,//属性数值

        storyTag: cc.Sprite,//剧情icon
    },

    onLoad() {
        this.addBtnEvent(this.btn);
    },

    showData() {
        let itemData = this._data;
        if (itemData) {
            let isUnlock = itemData.have;
            let cfgData = itemData.cfg;

            let bDefault = null == cfgData;
            //名字
            this.lblName.string = bDefault ? i18n.t("COMMON_DEFAULT") : cfgData.name;

            if(!bDefault)
                this.urlItem.url = UIUtils.uiHelps.getHeroDressIcon(cfgData.model); //时装图标
                
            //锁节点--unlock==1时显示
            // this.nodeLock.active = (!isUnlock && 1 == cfgData.unlock);
            // {//金币节点--unlock==2时显示
            //     this.nodeGold.active = (!isUnlock && 2 == cfgData.unlock);
            //     //金币数量
            //     this.lblGold.string = cfgData.money.count ? cfgData.money.count + "": "";
            //     let id = cfgData.money.itemid;//id==1为金币
            //     this.itemUrl.node.active = (id && 1 != id);
            //     this.nodeGoldImg.active = (1 == id);
            //     this.itemUrl.node.active && (this.itemUrl.url = UIUtils.uiHelps.getItemSlot(id));
            // }
            // {//属性节点
            //     this.nodeProp.active = false;//cfgData.prop && cfgData.prop.length > 0;
            //     if (cfgData.prop && cfgData.prop.length > 0){
            //         if (1 == cfgData.prop_type) {
            //             this.lblProp.string = "+" + cfgData.prop[0].value;
            //             this.propimg.url = UIUtils.uiHelps.getLangSp(cfgData.prop[0].prop);
            //         } else {
            //             this.lblProp.string = "+" + cfgData.prop[0].value / 100 + "%";
            //             this.propimg.url = UIUtils.uiHelps.getClotheProImg(cfgData.prop_type, cfgData.prop[0].prop);
            //         }
            //     }
            // }
            //背景置灰
            //ShaderUtils.shaderUtils.setImageGray(this.spBG, !isUnlock);
            //ShaderUtils.shaderUtils.setImageGray(this.spItem, !isUnlock);
            if (!isUnlock) {
                this.spBG.node.color = cc.color(144,144,144);
                this.spItem.node.color = cc.color(144,144,144);
            } else {
                this.spBG.node.color = cc.color(255,255,255);
                this.spItem.node.color = cc.color(255,255,255);
            }
        }
    },
});
