let Initializer = require("Initializer");
var List = require("List");
var Utils = require("Utils");
import { MINIGAMETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        nTitleResult: cc.Node,
        nTitleGameOver: cc.Node,
        listView:List,
        lblTitle:cc.Label,
        lblTitle2:cc.Label,
        nodeRewardTitle:cc.Node,
        nodeGameOver:cc.Node,
        lblTimes:cc.Label,
        listView2:List,
        btnNext:cc.Button,
    },

    ctor(){
        this.callback = null;
    },

    onLoad: function() {
        Initializer.timeProxy.itemReward = null;
        facade.subscribe("UPDATE_INVITE_INFO", this.updateInviteInfo, this);
        var score = this.node.openParam.score;
        var eventid = this.node.openParam.eventid;
        var gameCount = this.node.openParam.gameCount;
        var metrialList = this.node.openParam.metrialList;
        this.callback = this.node.openParam.func;
        let type = this.node.openParam.type;
        let cityid = this.node.openParam.cityid;
        if(type == 0) {
            this.nTitleResult.active = true;
            this.nTitleGameOver.active = false;
            this.lblTitle.string = i18n.t("FISH_TIPS16");
            this.lblTitle2.string = i18n.t("FISH_TIPS17");
        } else {
            this.nTitleResult.active = false;
            this.nTitleGameOver.active = true;
            this.lblTitle.string = i18n.t("FISH_TIPS14");
            this.lblTitle2.string = i18n.t("FISH_TIPS15");
        }
        
        if (gameCount < 3 || this.callback == null) {
            this.nodeGameOver.active = false;
        }
        else{
            this.lblTimes.string = Initializer.servantProxy.inviteBaseInfo.inviteCount + "/3";
        }
        let cfg = localcache.getItem(localdb.table_games,eventid);
        let gameRwdList = localcache.getFilters(localdb.table_game_rwd,"type",cfg.type);
        let listData = []
        for (var ii = gameRwdList.length-1; ii >= 0; ii--){
            let cg = gameRwdList[ii];
            if (score >= cg.score){
                listData = Utils.utils.clone(cg.rwd);
                break;
            }
        }
        for (var ii = 1; ii <= gameCount;ii++){
            let cg = cfg["rwd" + ii];
            for (var jj = 0; jj < cg.length;jj++){
                listData.push({extra:true,kind:cg[jj].kind,id:cg[jj].id,count:cg[jj].count});
            }
        }
        this.listView.data = listData;
        let listData2 = [];
        for (var ii = 0; ii < metrialList.length; ii++){
            listData2.push({id:metrialList[ii],kind:400,count:1})
        }
        this.listView2.data = listData2;
        let bLimit = cfg.type == 2 || cfg.type == 4;
        let endTime = Initializer.servantProxy.inviteEventData.refreshTime + Number(cfg.start);
        let bFinished = Utils.timeUtil.getCurSceond() >= endTime || (null != Initializer.servantProxy.inviteEventData.joinLimitEvent[cityid]
             && null != Initializer.servantProxy.inviteEventData.joinLimitEvent[cityid][eventid]);
        this.btnNext.interactable = !(bLimit && bFinished);
    },

    updateInviteInfo(){
        this.lblTimes.string = Initializer.servantProxy.inviteBaseInfo.inviteCount + "/3";
    },

    //关闭
    onClickClose: function() {
        Utils.utils.closeView(this, !0);
        Utils.utils.closeNameView("spaceGame/FishGameView");
        Utils.utils.closeNameView("spaceGame/FoodChangeView");
    },

    onClickBac(){
        if (this.nodeGameOver.active) return;
        this.onClickClose();
    },

    /**再来一局*/
    onClickAgain(){
        // if (Initializer.servantProxy.inviteBaseInfo.inviteCount <= 0){
        //     return;
        // }
        if (!Initializer.servantProxy.isCanUseInvite()) return;
        if (this.callback) this.callback();
        Utils.utils.closeView(this, !0);
    },

});
