var scUrlLoad = require("UrlLoad");
var scRenderListItem = require("RenderListItem");
var scInitializer = require("Initializer");
var scUIUtils = require("UIUtils");
var shaderUtils = require("ShaderUtils");

cc.Class({
    extends: scRenderListItem,

    properties: {
        urlAvatar: scUrlLoad,
        lbProp: cc.Label,
        lbPropNum: cc.Label,
        lbRemainTimes: cc.Label,
        btn: cc.Button,
        nGraies: [cc.Sprite],
    },

    ctor() {},

    showData() {
        var data = this._data;
        if (data) {
            this.urlAvatar.url = scUIUtils.uiHelps.getServantHead(data.id);
            let bossData = localcache.getItem(localdb.table_unionBoss, scInitializer.unionProxy.bossInfo.currentCbid);
            this.lbProp.string = scUIUtils.uiHelps.getPinzhiStr(bossData.ep);
            let allEpData = scInitializer.playerProxy.allEpData;
            let ep = "e" + bossData.ep;
            let tmpArray = [];
            let servantList = scInitializer.servantProxy.getServantList();
            for (var j = 0, jLen = servantList ? servantList.length : 0; j < jLen; j++) {
                let heroInfo = servantList[j];
                if (null != heroInfo) {
                    tmpArray.push(1);
                }
            }
            // 伤害 = 伙伴该属性 + 向上取整((时装该属性 + 徒弟该属性) / 拥有伙伴人数);
            let num = data.aep[ep] + Math.ceil((scInitializer.playerProxy.clotheDamage[ep] + allEpData["sonaddep"][ep]) / servantList.length);
            let brocadeInfoData = scInitializer.clotheProxy.brocadeInfoData;
            if(bossData.ep == 1 && null != brocadeInfoData && null != brocadeInfoData.extraProp && null != brocadeInfoData.extraProp[5]) {   
                num += brocadeInfoData.extraProp[5];
            }
            this.lbPropNum.string = num;
            if(null == data.fightInfo) {
                //初始每个伙伴有1次机会
                this.lbRemainTimes.string = i18n.t("BOSS_SHENG_YU_CI_SHU") + 1;
            } else {
                //每场boss战每个伙伴有1次复活机会
                this.lbRemainTimes.string = i18n.t("BOSS_SHENG_YU_CI_SHU") + (data.fightInfo.h == 0 ? 0 : 1);
            }
            for(let i = 0, len = this.nGraies.length; i < len; i++) {
                shaderUtils.shaderUtils.setImageGray(this.nGraies[i], null != data.fightInfo && data.fightInfo.h == 0);
            }
        }
    },

    anchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultServantY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        } 
    },

    onLoad () {
        this.addBtnEvent(this.btn);
    },
});
