let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Utils = require("Utils");
var Initializer = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        nodeRole: cc.Node,
        bgUrl: UrlLoad,//背景图
        servantShow: UrlLoad,//角色
        lblType:cc.Label,//时装title
        lblInfo: cc.Label,//底部详情说明
        ndParam:cc.Node,//属性节点
        spParam:[UrlLoad],//属性背景图
        lbParam:[cc.Label],//属性文本
        arrLbProp: [cc.Label],
        lbGet:cc.Label
    },
    onLoad(){
        let heroInfo = this.node.openParam;
        if(heroInfo && null != heroInfo.id){
            this.showHeroClothesInfo(heroInfo.id);
        }
    },
    showHeroClothesInfo(dressID) {
        let heroDresssArray = localcache.getFilters(localdb.table_heroDress, "id", dressID);
        if(heroDresssArray && heroDresssArray.length > 0){
            let cfgData = heroDresssArray[0];
            let heroClothesInfo = Initializer.playerProxy.userClothe;
            let bgID = heroClothesInfo ? heroClothesInfo.background: 0;
            this.bgUrl.node.active = 0 != bgID;
            if (0 != bgID) {
                let o = localcache.getItem(localdb.table_userClothe,bgID);
                o && (this.bgUrl.url = UIUtils.uiHelps.getStoryBg(o.model));
            }
            this.updateShow(cfgData);
        }
    },
    onClickBack() {
        Utils.utils.closeView(this);
    },
    //更新显示
    updateShow(cfgData) {
        if (null != cfgData){
            this.lblType.string = cfgData.name;
            this.lbGet.string = cfgData.text;
            //修改伙伴图像显示
            this.servantShow.url = UIUtils.uiHelps.getServantSkinSpine(cfgData.model);
            //完善底部说明
            this.lblInfo.string = cfgData.des;//详情说明
            //属性说明
            this.ndParam.active = cfgData.prop && cfgData.prop.length > 0;
            if (cfgData.prop && cfgData.prop.length > 0){
                for(let i = 0;i < this.spParam.length;i++){
                    this.spParam[i].node.parent.active = false;
                    if(i < cfgData.prop.length){
                        this.spParam[i].node.parent.active = true;
                        if (1 == cfgData.prop_type) {
                            this.lbParam[i].string = "" + cfgData.prop[i].value;
                            this.spParam[i].url = UIUtils.uiHelps.getLangSp(cfgData.prop[i].prop);
                        } else {
                            this.lbParam[i].string = "" + cfgData.prop[i].value / 100 + "%";
                            this.spParam[i].url = UIUtils.uiHelps.getClotheProImg(cfgData.prop_type, cfgData.prop[i].prop);
                        }
                        this.arrLbProp[i].string = UIUtils.uiHelps.getPinzhiStr(cfgData.prop[i].prop);
                    }
                }
            }
        }
    },
});
