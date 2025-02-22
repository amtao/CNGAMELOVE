var initializer = require("Initializer");
var utils = require("Utils");
var List = require("List");
var nobleOrderRewardRender = require("nobleOrderRewardRender");
var UIUtils = require("UIUtils");
var urlLoad = require("UrlLoad");
var nobleOrderRankRender = require("nobleOrderRankRender");

cc.Class({
    extends: cc.Component,

    properties: {
        rewardList: List,
        rewardScrollView: cc.ScrollView,
        specialReward: nobleOrderRewardRender,
        tabNodes: [cc.Node],
        rewardContent: cc.Node,
        taskContent: cc.Node,
        rankContent: cc.Node,
        normalTaskList: List,
        specialTaskList: List,
        normalTaskNode: cc.Node,
        specialTaskNode: cc.Node,
        normalTaskBtnNode: cc.Node,
        specialTaskBtnNode: cc.Node,
        weekProgressNode: cc.Node,
        weekProgress: cc.ProgressBar,
        weekProgressLabel: cc.Label,
        rankList: List,
        levelProgress: cc.ProgressBar,
        levelProgressLabel: cc.Label,
        levelLabel: cc.Label,
        endTime: cc.Label,
        //iconSprite: urlLoad,
        getRewardButton: cc.Button,
        advancedButton: cc.Button,
        myRankRender: nobleOrderRankRender,
        //infoNode: cc.Node,
        urlAvatar: urlLoad,
        lbSuit: cc.Label,
        //iconSpineNode: cc.Node
    },

    onLoad () {
        this.curSpecialRewardLevel = 0;
        this.rewardScrollView.node.on("scrolling", this.onScroll, this);
        facade.subscribe("NOBLE_ORDER_DATA_UPDATE", this.onShowData, this);
        facade.subscribe("NOBLE_ORDER_RANK_UPDATE", this.onRankData, this);
        facade.subscribe("NOBLE_ORDER_REWARD_UPDATE", this.updateGetRewardBtn, this);
        facade.subscribe("NOBLR_ORDER_MYRID_UPDATE", this.showMyRankData, this);
        facade.subscribe("PLAYER_SHOW_CHANGE_UPDATE", this.getRankData, this);
        facade.subscribe("PLAYER_UPDATE_HEAD", this.getRankData, this);
        initializer.nobleOrderProxy.sendOpen();
        this.onTaskTabClick(null, 1);
        this.updateGetRewardBtn();
        // var size = cc.view.getVisibleSize();
        // if (size.height > 1280) {
        //     this.infoNode.y = 526 +parseInt( (size.height - 1280) / 6);
        // }
    },
    onShowData () {
        var info = initializer.nobleOrderProxy.data;
        if (!info) return;
        this.showIcon();
        this.updateAdvancedBtn();
        this.showReward();
        this.showTask();
        this.showExpProgress();
        this.showLevelProgress();
        this.scrollToLevel();
        this.showMyRankData();
        UIUtils.uiUtils.countDown(info.info.eTime, this.endTime,
        () => {
            utils.timeUtil.second >= info.info.eTime && (this.endTime.string = i18n.t("ACTHD_OVERDUE"));
        });

        // 活动结束按钮隐藏
        var s = info.info.eTime - utils.timeUtil.second;
        if (s <= 0) {
            this.advancedButton.node.active = false;
        }

        let suitId = localcache.getFilter(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID
         ? localdb.table_magnate_param : localdb.table_magnate_new_param, "name", "cloth_name");
        let suitData = localcache.getItem(localdb.table_usersuit, parseInt(suitId.param));
        this.lbSuit.string = suitData.name;
        initializer.playerProxy.loadPlayerSpinePrefab(this.urlAvatar, { suitId: suitData.id });
    },
    
    updateGetRewardBtn () {
        this.getRewardButton.active = initializer.nobleOrderProxy.canGetReward;
    },

    updateAdvancedBtn () {
        this.advancedButton.interactable = initializer.nobleOrderProxy.data.levelUp == 0;
        this.advancedButton.node.active = initializer.nobleOrderProxy.data.levelUp == 0;
    },

    showIcon () {
        // var data = initializer.nobleOrderProxy.data;
        // var icon = initializer.nobleOrderProxy.getIcon(data.levelUp === 1);
        // this.iconSprite.url = icon;
        // this.iconSpineNode.active = data.levelUp === 1;
    },

    onRankData () {
        //this.rankList.data = initializer.nobleOrderProxy.rankData;
    },

    showMyRankData () {
        var myRid = initializer.nobleOrderProxy.myRid;
        if (!myRid || !initializer.nobleOrderProxy.data) return;
        var data = {};
        utils.utils.copyData(data, myRid);
        data.score = initializer.nobleOrderProxy.data.level;
        //this.myRankRender.data = data;
    },


    showReward () {
        var rewardList;
        if(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID){
            rewardList = localcache.getList(localdb.table_magnate_rwd);
        }else{
            rewardList = localcache.getList(localdb.table_magnate_new_rwd);
        }
        this.rewardList.data = rewardList;
        this.rewardList.updateRenders();
    },

    showTask () {
        var normalTask;
        var specialTask;
        if(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID){
            normalTask = localcache.getGroup(localdb.table_magnate_task, "type", 1);
            specialTask = localcache.getGroup(localdb.table_magnate_task, "type", 2);
        }else{
            normalTask = localcache.getGroup(localdb.table_magnate_new_task, "type", 1);
            specialTask = localcache.getGroup(localdb.table_magnate_new_task, "type", 2);
        }
        normalTask = normalTask.filter((data) => {
            return data.is_show == 1;
        });
        specialTask = specialTask.filter((data) => {
            return data.is_show == 1;
        });
        this.normalTaskList.data = normalTask;
        this.specialTaskList.data = specialTask;
    },

    onClickClose () {
        utils.utils.closeView(this);
    },

    scrollToLevel () {
        var curLevel = initializer.nobleOrderProxy.data.level;
        var scrollOffset;
        if(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID){
            scrollOffset = (curLevel - 1) * 130;
        }else{
            scrollOffset = (curLevel - 1) * 117;
        }
        this.scheduleOnce(()=>{
            this.rewardScrollView.scrollToOffset(cc.v2(0, scrollOffset), 0.1);
        },0.1)
        this.curSpecialRewardLevel = this.getNextSpecialReward(curLevel);
        this.showSpecialReward();
    },

    onScroll () {
        if (!initializer.nobleOrderProxy.data) return;
        var offset = this.rewardScrollView.getScrollOffset();
        var scrollLastLevel;
        if(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID){
            scrollLastLevel = Math.floor(offset.y / 130);
        }else{
            scrollLastLevel = Math.floor(offset.y / 105);
        }
        var rewardLevel = this.getNextSpecialReward(scrollLastLevel);
        if (this.curSpecialRewardLevel != rewardLevel) {
            this.curSpecialRewardLevel = rewardLevel;
            this.showSpecialReward();
        }
    },

    getNextSpecialReward (scrollLastLevel) {
        var specialLevelList = initializer.nobleOrderProxy.getSpecialRewardLevelList();
        for (var i = 0; i < specialLevelList.length; i++) {
            if (specialLevelList[i] > scrollLastLevel) return specialLevelList[i];
        }
        return specialLevelList[specialLevelList.length - 1];
    },

    showSpecialReward () {
        var reward;
        if(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID){
            reward = localcache.getItem(localdb.table_magnate_rwd, this.curSpecialRewardLevel);
        }else{
            reward = localcache.getItem(localdb.table_magnate_new_rwd, this.curSpecialRewardLevel);
        }
        if (reward) {
            this.specialReward.data = reward;
        }
    },

    onTabClick (e, data) {
        switch (parseInt(data)) {
            case 1:
                this.rewardContent.active = true;
                this.taskContent.active = false;
                //this.rankContent.active = false;
                break;
            case 2:
                this.rewardContent.active = false;
                this.taskContent.active = true;
                this.normalTaskNode.active = true
                this.specialTaskNode.active = false
                //this.rankContent.active = false;
                break;
            case 3:
                this.rewardContent.active = false;
                this.taskContent.active = false;
                //this.rankContent.active = true;
                this.getRankData();
                break;
            case 4:
                this.rewardContent.active = false;
                this.taskContent.active = true;
                this.normalTaskNode.active = false
                this.specialTaskNode.active = true
                break;
        }
        this.currentType = data? parseInt(data) : 1;
        for (var i = 0; i < this.tabNodes.length; i++) {
            var selected = this.tabNodes[i].getChildByName("selected");
            selected.active = e.target === this.tabNodes[i];
        }
    },

    onTaskTabClick (e, data) {
        return
        var tabIndex = parseInt(data)   // 1 日常任务 2 巅峰任务
        this.normalTaskNode.active = tabIndex == 1;
        this.normalTaskBtnNode.active = tabIndex != 1;
        this.specialTaskNode.active = tabIndex != 1;
        this.specialTaskBtnNode.active = tabIndex == 1;
    },

    onGetExpClick () {
      var e = {
          target: this.tabNodes[1]
      }
      this.onTabClick(e, 2);
    },

    showExpProgress () {
        var nobleOrderData = initializer.nobleOrderProxy.data;
        if (!nobleOrderData) return;
        var data;
        if(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID){
            data = localcache.getItem(localdb.table_magnate_param, 6);
        }else{
            data = localcache.getItem(localdb.table_magnate_new_param, 6);
        }
        var maxLevel = initializer.nobleOrderProxy.getMaxLevel();
        var maxExp = parseInt(data.param);
        if( maxExp != 0) {
            // 满级
            if (nobleOrderData.level >= maxLevel) {
                this.weekProgressLabel.string = i18n.t("GRL_FULL_LEVEL");
                this.weekProgress.progress = 1;
                return;
            }
            this.weekProgressLabel.string = nobleOrderData.weekExp + "/" + maxExp;
            this.weekProgress.progress = Math.round(nobleOrderData.weekExp/ maxExp);
        }
        this.weekProgressNode.active = maxExp != 0;
    },

    showLevelProgress () {
        var nobleOrderData = initializer.nobleOrderProxy.data;
        if (!nobleOrderData) return;
        this.levelLabel.string = nobleOrderData.level;
        var maxLevel = initializer.nobleOrderProxy.getMaxLevel();
        if (nobleOrderData.level >= maxLevel) {
            this.levelProgress.progress = 1;
            this.levelProgressLabel.string = i18n.t("GRL_FULL_LEVEL");
            return;
        }
        var levelExpData;
        if(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID){
            levelExpData = localcache.getItem(localdb.table_magnate_lv, nobleOrderData.level);
        }else{
            levelExpData = localcache.getItem(localdb.table_magnate_new_lv, nobleOrderData.level);
        }
        if (!levelExpData) return;
        var num = nobleOrderData.exp / levelExpData.exp.toFixed(2);
        this.levelProgress.progress = num;
        this.levelProgressLabel.string = nobleOrderData.exp + "/" + levelExpData.exp;
    },

    getRankData () {
        initializer.nobleOrderProxy.getRankData();
    },

    onBuyLevelClick () {
        var nobleOrderData = initializer.nobleOrderProxy.data;
        if (!nobleOrderData) return;
        if (nobleOrderData.level >= initializer.nobleOrderProxy.getMaxLevel()) {
            utils.alertUtil.alert(i18n.t('GRL_LEVEL_MAX_TIP'));
            return;
        }
        utils.utils.openPrefabView("nobleOrderNew/buyNewNobleOrderLevel");
    },

    onGetAllRewardClick() {
        initializer.nobleOrderProxy.sendGetAllReward();
    },

    onRewardPreviewClick () {
        utils.utils.openPrefabView("nobleOrderNew/nobleOrderRewardPreview");
    },

    onRankRwdClick () {
        //utils.utils.openPrefabView("nobleOrder/nobleOrderRankRwd");
    },

    onAdvanceClick () {
        utils.utils.openPrefabView("nobleOrderNew/nobleOrderLeveUpRWD");
        //initializer.nobleOrderProxy.buyAdvancedOrder();
    }
});
