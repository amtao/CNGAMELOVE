var scRenderListItem = require("RenderListItem");
var scInitializer = require("Initializer");
var scUtils = require("Utils");
var scUrlLoad = require("UrlLoad");
var scUIUtils = require("UIUtils");
var shaderUtils = require("ShaderUtils");
import { UNION_BOSS_CD } from "GameDefine";

cc.Class({
    extends: scRenderListItem,

    properties: {
        lbName: cc.Label,
        roleAvatar: scUrlLoad,
        nLock: cc.Node,
        lbLock: cc.Label,
        spGraies: [cc.Sprite],
        nSelected: cc.Node,
    },

    showData() {
        var data = this.data;
        if (data) {
            this.lbName.string = i18n.t("CLOTHE_PVE_GATE", { d: data.id }); 
            this.roleAvatar.url = scUIUtils.uiHelps.getServantHead(data.image);
            this.bLock = data.level > scInitializer.unionProxy.clubInfo.jytLv;
            this.nLock.active = this.bLock;
            this.lbLock.string = i18n.t("UNION_COPY_LOCK", { num: data.level });
           
            let bossInfo = scInitializer.unionProxy.bossInfo;
            if(null == bossInfo) {
                return;
            }
            let bStart = bossInfo.startBossTime != 0 && bossInfo.startBossTime + UNION_BOSS_CD > scUtils.timeUtil.second;
            this.curBossId = bStart ? bossInfo.currentCbid : 1;
            for(let i = 0, len = this.spGraies.length; i < len; i++) {
                shaderUtils.shaderUtils.setImageGray(this.spGraies[i], bStart && (data.id < this.curBossId || (data.id == this.curBossId && bossInfo.bosshp <= 0)));
            }
            this.nSelected.active = bStart && data.id == this.curBossId && bossInfo.bosshp > 0;
        }
    },

    onClickItem() {
        if(this.bLock) {
            scUtils.alertUtil.alert18n("UNION_COPY_LOCK2");
        } else if(this.data.id > this.curBossId) {
            scUtils.alertUtil.alert18n("UNION_COPY_TIPS");
        } else if(this.data.id < this.curBossId) {
            scUtils.alertUtil.alert18n("UNION_COPY_DEATH");
        }
    },
});
