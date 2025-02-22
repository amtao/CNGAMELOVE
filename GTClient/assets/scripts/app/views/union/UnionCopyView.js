var scList = require("List");
var scInitializer = require("Initializer");
var scUtils = require("Utils");
var scUrlLoad = require("UrlLoad");
var scUIUtils = require("UIUtils");
import { UNION_BOSS_CD } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        listScroll: cc.ScrollView,
        list: scList,
        urlProp: scUrlLoad,
        lbName: cc.Label,
        urlAvatar: scUrlLoad,
        pgBlood: cc.ProgressBar,
        lbLeftBlood: cc.Label,
        lbExp: cc.Label,
        lbContribution: cc.Label,
        lbTimeTitle: cc.Label,
        lbTime: cc.Label,
        btnOpen: cc.Node,
        btnStart: cc.Node,
        lbStart: cc.Label,
        nFinished: cc.Node,
    },

    onLoad() {
        facade.subscribe("UPDATE_BOSS_INFO", this.onUpdateData, this);
        this.onUpdateData();
    },

    onUpdateData() {
        let bossInfo = scInitializer.unionProxy.bossInfo;
        if(null == bossInfo) {
            return;
        }
        let bStart = bossInfo.startBossTime != 0 && bossInfo.startBossTime + UNION_BOSS_CD > scUtils.timeUtil.second;
        let bossData = localcache.getItem(localdb.table_unionBoss, bStart ? bossInfo.currentCbid : 1);
        this.bossData = bossData;
        this.urlProp.url = scUIUtils.uiHelps.getUICardPic("kpsj_icon_" + bossData.ep);
        this.lbName.string = bossData.name;

        let self = this;
        this.urlAvatar.loadHandle = () => {
            self.urlAvatar.node.y = -self.urlAvatar.node.children[0].height;
        };
        this.urlAvatar.url = scUIUtils.uiHelps.getServantSpine(bossData.image);

        let bossHp = bossInfo.bosshp < 0 ? 0 : bossInfo.bosshp;
        bossHp = bStart ? bossHp : bossData.hp;
        this.pgBlood.progress = bossHp / bossData.hp;
        this.lbLeftBlood.string = i18n.t("COMMON_NUM", { f: bossHp, s:bossData.hp });

        this.lbExp.string = bossData.rwd[0].count;
        this.lbContribution.string = bossData.rwd_personal[0].count;

        if(bStart) { //已开启
            this.btnOpen.active = false;
            let bFinished = bossInfo.bosshp <= 0;
            this.lbTimeTitle.string = i18n.t("ACADEMY_SHENG_YU_TIME");
            scUIUtils.uiUtils.countDown(bossInfo.startBossTime + UNION_BOSS_CD, this.lbTime, () => {
                self.onUpdateData();
            });
            this.btnOpen.active = false;
            this.btnStart.active = !bFinished;
            this.lbStart.string = i18n.t(bossInfo.bosshp < bossData.hp ? "CLUB_COPY_CONTINUE" : "CLUB_COPY_START");
            this.nFinished.active = bFinished;
        } else { //未开启
            let openTime = scUtils.utils.getParamStr("club_bossOpenTime").split("|");
            let endTime = scUtils.utils.getParamStr("club_bossEndTime").split("|");
            this.lbTimeTitle.string = i18n.t("CLUB_COPY_OPEN_TIME");
            this.lbTime.string = openTime[0] + ":" + openTime[1] + "-" + endTime[0] + ":" + endTime[1];
            this.btnOpen.active = true;
            this.btnStart.active = this.nFinished.active = false;
        }

        this.list.data = localcache.getList(localdb.table_unionBoss);
        this.scrollToBoss();
    },

    scrollToBoss () {
        let bossInfo = scInitializer.unionProxy.bossInfo;
        if(null == bossInfo) {
            return;
        }
        let bStart = bossInfo.startBossTime != 0 && bossInfo.startBossTime + UNION_BOSS_CD > scUtils.timeUtil.second;
        if(!bStart) {
            return;
        }
        var curBoss = bossInfo.currentCbid;
        let count = (curBoss - 1 - Math.floor(this.list.bufferZone / 2));
        if(count < 0) {
            count = 0;
        }
        var scrollOffset = count * (this.list.item.node.width + this.list.spaceX);
        let self = this;
        this.scheduleOnce(() => {
            self.listScroll.scrollToOffset(cc.v2(scrollOffset, 0), 0.1);
        }, 0.1);
    },

    onClickOpen: function() {
        if (scInitializer.unionProxy.memberInfo.post != 1 && scInitializer.unionProxy.memberInfo.post != 2) {
            scUtils.alertUtil.alert18n("UNION_COPY_OPEN_LIMIT");
            return;
        }
        if(!scInitializer.unionProxy.checkNewTime()) {
            scUtils.alertUtil.alert(i18n.t("UNION_BOSS_CD"));
            return;
        }
        let openTime = scUtils.utils.getParamStr("club_bossOpenTime").split("|");
        let endTime = scUtils.utils.getParamStr("club_bossEndTime").split("|");
        let now = new Date(scUtils.timeUtil.second * 1000);
        let nowHour = now.getHours();
        if(nowHour < openTime[0] || nowHour >= endTime[0]) {
            scUtils.alertUtil.alert(i18n.t("UNION_COPY_TIME_PASS"));
            return;
        }
        scUtils.utils.openPrefabView("union/UnionOpenBossView");
    },

    onClickStart: function() {
        if(!scInitializer.unionProxy.checkNewTime()) {
            scUtils.alertUtil.alert(i18n.t("UNION_BOSS_CD"));
            return;
        }
        let bossInfo = scInitializer.unionProxy.bossInfo;
        if(null == bossInfo) {
            return;
        }
        if(scUtils.timeUtil.second > bossInfo.startBossTime + UNION_BOSS_CD) {
            scUtils.alertUtil.alert(i18n.t("UNION_COPY_TIME_PASS"));
            return;
        }
        scUtils.utils.openPrefabView("union/UnionBossView");
    },

    onClickRank: function() {
        scInitializer.unionProxy.reqRankList();
    },

    onClickClose: function() {
        scUtils.utils.closeView(this);
    },
});
