let scRenderListItem = require("RenderListItem");
let scUrlLoad = require("UrlLoad");
let scUIUtils = require("UIUtils");
let scInitializer = require("Initializer");
let scShaderUtils = require("ShaderUtils");

cc.Class({
    extends: scRenderListItem,

    properties: {
        spIcon: scUrlLoad,
        lbName: cc.Label,
        lbType: cc.Label,
        nProp: cc.Node,
        urlProp: scUrlLoad,
        lbProp: cc.Label,
        lbPropNum: cc.Label,
        nSelected: cc.Node,
        select: {
            set: function(bShow) {
                this.nSelected && (this.nSelected.active = bShow);
            },
            enumerable: !0,
            configurable: !0
        },
    },

    showData() {
        let data = this._data;
        if(data) {
            let modelPart = data.model.split("|");
            this.spIcon.url = scUIUtils.uiHelps.getRolePart(modelPart[0]);
            let sprite = this.spIcon.getComponent(cc.Sprite);
            scShaderUtils.shaderUtils.setImageGray(sprite, !scInitializer.playerProxy.isUnlockCloth(data.id));
            this.lbName.string = data.name;
            //this.lbType.string = i18n.t("USER_CLOTHE_" + data.part);
            let propData = data.prop;
            let bHasProp = propData && propData.length > 0;
            this.nProp.active = bHasProp;
            if (bHasProp) {
                let firstProp = propData[0];
                if (1 == data.prop_type) {
                    this.urlProp.url = scUIUtils.uiHelps.getUserclothePic("prop_" + firstProp.prop);
                    //this.lbProp.string = scUIUtils.uiHelps.getPinzhiStr(firstProp.prop);
                    this.lbPropNum.string = "+" + firstProp.value;        
                } else {
                    this.urlProp.url = scUIUtils.uiHelps.getClotheProImg(data.prop_type, firstProp.prop);
                    //this.lbProp.string = scUIUtils.uiHelps.getClotheProStr(data.prop_type, firstProp.prop);
                    let epData = scInitializer.playerProxy.getUserEpData(data.prop_type);
                    let addPerInfo = Math.floor(firstProp.value / 100);
                    let propEpData = scInitializer.playerProxy.getPropDataByIndex(firstProp.prop, epData, addPerInfo * 0.01);
                    this.lbPropNum.string = "+" + addPerInfo + "% (" + propEpData + ")";
                }
            }
        }
    },
    
});
