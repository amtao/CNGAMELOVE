let initializer = require("Initializer");
var utils = require("Utils");
var bagProxy = require("BagProxy");
let uiUtils = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,//工会名称
        lblFund: cc.Label,//工会资金
        lblNum: cc.Label,//工会成员
        lblId: cc.Label,//工会ID
        lblExp: cc.Label,//工会经验
        lblPos: cc.Label,//工会职位
        lblGx: cc.Label,//个人贡献
        lblLevel: cc.Label,//工会等级
        lblChat: cc.Label,//最新聊天消息内容
        lblNameChat: cc.Label,//最新聊天消息名字
        lblMaster: cc.Label,//会长信息--新版本不显示
        lblRenown: cc.Label,//工会声望
        redCopy: cc.Node,
        managerNode: cc.Node,//宫殿管理
        lblTravelLv: cc.Label,//宫殿游历等级
        lblBossLv: cc.Label,//复仇之路等级
        lblConvertLV: cc.Label,//贡献商店等级
        lblDevoteLv: cc.Label,//主殿等级
        lblTaskLV: cc.Label,//收纳府等级
        lblNobilityLv: cc.Label,//权贵大厅等级

        lbShili: cc.Label,
        lbGongxian: cc.Label,
        lbNotice: cc.Label,
        pgLevel: cc.ProgressBar,

        nodeYanhui: cc.Node,
        nodeShop: cc.Node,
        nodeJianyan: cc.Node,
        nodeLishi: cc.Node,
        nodeNeiwu: cc.Node,

        nodeExit:cc.Node,
    },
    ctor() {
        this.flag = !1;
        this.pos = [];
        this.isUpdatefloat = false
        this.updateIndex = 0
        this.m_x = 300
    },
    updateTime(){
        if(null == initializer.unionProxy.clubInfo) {
            return;
        }
        if(initializer.unionProxy.clubInfo.dissolutionTime>0){
            this.isUpdatefloat = true
        }else{
            this.isUpdatefloat = false
        }
    },
    onLoad() {
        // initializer.unionProxy.clubInfo.dissolutionTime = 3600
        // initializer.unionProxy.clubInfo.dissolutionTime += 3
        
        facade.subscribe("UNION_CLOSE_MAIN", this.eventClose, this);
        facade.subscribe("UPDATE_MEMBER_INFO", this.UPDATE_MEMBER_INFO, this);
        facade.subscribe("UPDATE_SEARCH_INFO", this.UPDATE_SEARCH_INFO, this);
        facade.subscribe(initializer.chatProxy.UPDATE_CLUB_MSG, this.UPDATE_CLUB_MSG, this);
        //facade.subscribe("UPDATE_BOSS_INFO", this.updateCopyRed, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.eventClose, this);
        
        this.UPDATE_SEARCH_INFO();
        this.UPDATE_MEMBER_INFO();
        this.UPDATE_CLUB_MSG();
        //this.updateCopyRed();

        uiUtils.uiUtils.floatPos(this.nodeYanhui, 0, 10, 4);
        uiUtils.uiUtils.floatPos(this.nodeShop, 0 , 10, 2);
        uiUtils.uiUtils.floatPos(this.nodeJianyan, 0 , 10, 4);
        uiUtils.uiUtils.floatPos(this.nodeLishi, 0 , 10, 3);
        uiUtils.uiUtils.floatPos(this.nodeNeiwu, 0 , 10, 2);


        initializer.unionProxy.sendgetThrowInfo()
    },

    UPDATE_SEARCH_INFO() {
        let clubInfo = initializer.unionProxy.clubInfo;
        if (clubInfo) {
            this.lblName.string = clubInfo.name;
            this.lblId.string = clubInfo.id + "";
            this.lblFund.string = clubInfo.fund + "";
            this.lblNum.string = i18n.t("COMMON_NUM", {
                f: clubInfo.members.length,
                s: initializer.unionProxy.getUnionLvMaxCount(clubInfo.level)
            });
            let levelData = localcache.getItem(localdb.table_union, clubInfo.level);       
            this.lblExp.string = i18n.t("COMMON_NUM", { f: clubInfo.exp, s: null == levelData.exp ? i18n.t("COMMON_MAX") : levelData.exp });
            this.pgLevel.progress = null == levelData.exp ? 1 : clubInfo.exp / levelData.exp;
            this.pgLevel.barSprite.node.active = clubInfo.exp > 0 || null == levelData.exp;

            this.lblLevel.string = i18n.t("UNION_LEVEL_TXT", {num: clubInfo.level});
            //this.lblRenown.string = initializer.unionProxy.getPrestige() + "";
            //盟主信息
            var e = initializer.unionProxy.getMengzhu(initializer.unionProxy.clubInfo.members);
            if (e) {
                this.lblMaster.string = e ? e.name: "";
            }
            //this.lblDevoteLv.string = i18n.t("UNION_LEVEL_TXT", {num: clubInfo.level});
            // this.lblTravelLv.string = i18n.t("UNION_LEVEL", {num: initializer.unionProxy.getTravelLv()});
            // this.lblTaskLV.string = i18n.t("UNION_LEVEL", {num: initializer.unionProxy.getTaskLv()});
            // this.lblBossLv.string = i18n.t("UNION_LEVEL", {num: initializer.unionProxy.getBossLv()});
            // this.lblConvertLV.string = i18n.t("UNION_LEVEL", {num: initializer.unionProxy.getConvertLv()});
            // this.lblNobilityLv.string = i18n.t("UNION_LEVEL", {num: initializer.unionProxy.getNobilityLv()});
            
            if(initializer.unionProxy.firstShowUnionNotice == false){
                initializer.unionProxy.firstShowUnionNotice = true;
                this.onClickShowNotice();
            }
            this.updateTime()
            this.lbNotice.string = i18n.has(clubInfo.notice) ? i18n.t(clubInfo.notice) : clubInfo.notice;
        }
    },
    UPDATE_MEMBER_INFO() {
        let myInfo = initializer.unionProxy.clubInfo.members.filter((data)=>{
            return data.id == initializer.playerProxy.userData.uid;
        });
        if(null != myInfo && myInfo.length > 0) {
            myInfo = myInfo[0];
            this.managerNode.active = (1 == myInfo.post || 2 == myInfo.post);
            this.lblPos.string = initializer.unionProxy.getPostion(myInfo.post);
            this.lbGongxian.string = initializer.bagProxy.getItemCount(118);
            this.nodeExit.active = myInfo.post != 1;
        } else {
            this.eventClose();
        }
        let shili = 0;
        let members = initializer.unionProxy.clubInfo.members;
        for(let i = 0, len = members.length; i < len; i++) {
            shili += members[i].shili;
        }
        this.lbShili.string = shili;
        // var t = initializer.unionProxy.memberInfo;
        // if (null == t || t.cid <= 0) 
        // else {
        //     this.lblPos.string = initializer.unionProxy.getPostion(t.post);
        //     //this.lblGx.string = utils.utils.formatMoney(t.leftgx);
        //     // this.lblGx.string = i18n.t("COMMON_NUM", {//个人贡献数据同元宝数据
        //     //     f: t.leftgx,
        //     //     s: t.allgx
        //     // });
        //     this.lbShili.string = t.shili;
        //     this.lbGongxian.string = t.gx;
        // }
        
        // var t = initializer.unionProxy.memberInfo;
        // this.managerNode.active = (1 == t.post || 2 == t.post);
    },
    setShowChat(t) {
        this.lblNameChat.string = t ? i18n.t("chat_home_show", {
            name: t.user ? t.user.name: i18n.t("CHAT_SYS_TIP")
        }) : "";
        this.lblChat.string = t ? initializer.chatProxy.getSpMsg(t.msg) : "";
    },
    UPDATE_CLUB_MSG() {
        this.setShowChat(initializer.chatProxy.getLastMsg(initializer.chatProxy.clubMsg));
    },
    eventClose() {
        utils.utils.closeView(this);
    },
    eventManage() {
        utils.utils.openPrefabView("union/UnionManage");
    },
    eventBuild() {
        // utils.utils.openPrefabView("union/UnionBuild");        
        initializer.unionProxy.sendGetUserClubInfo(function(){
            utils.utils.openPrefabView("union/UnionTaskView");
        })
    },
    eventMembers() {
        utils.utils.openPrefabView("union/UnionMebInfo");
    },
    eventExchange() {      
        //initializer.unionProxy.sendShopList();
        utils.utils.openPrefabView("union/UnionShopView");
    },
    eventCopy() {
        initializer.unionProxy.sendGetUserClubInfo(() => {
            initializer.unionProxy.sendBossList();
        });
    },
    eventCrossFight() {
        utils.utils.openPrefabView("union/");
    },
    eventRank() {
        initializer.unionProxy.sendRankList(initializer.unionProxy.memberInfo.cid);
    },
    eventChat() {
        utils.utils.openPrefabView("chat/ChatView", !1, {
            type: 2
        });
    },

    onClickMReward() {
        utils.utils.openPrefabView("union/UnionMonthRwdView");
    },

    onClickDReward(){
        initializer.unionProxy.sendGetUserClubInfo(function(){
            utils.utils.openPrefabView("union/UnionTaskView");
        })
    },
    onClickShowNotice(){
        utils.utils.openPrefabView("union/UnionNotice");
    },
    onClickEvent(){
        initializer.unionProxy.sendGetUserClubInfo(() => {
            utils.utils.openPrefabView("union/UnionRecordView");
        });
        // initializer.unionProxy.sendGetUserClubInfo(function(){
        //     utils.utils.openPrefabView("union/UnionEventView");
        // })
    },

    onClickYouli(){
        //utils.alertUtil.alert18n("COMMON_ZANWEIKAIQI");
        //initializer.unionProxy.sendGetPartyBaseInfo()
              
        initializer.unionProxy.sendGetPartyBaseInfo(function(){
            utils.utils.openPrefabView("union/Partybegins");
        })
    },

    onClickNobility(){
        initializer.unionProxy.sendGetUserClubInfo(function(){
            initializer.unionProxy.sendGetBigwigList(function(){
                utils.utils.openPrefabView("unionNobility/UnionNobilityMainView");
            });
        })
    },

    onClickZhongsheng(){
        utils.utils.openPrefabView("union/UnionInteriorView");
        // initializer.unionProxy.sendGetUserClubInfo(function(){
        //     initializer.unionProxy.sendClubList(function(){
        //         utils.utils.openPrefabView("union/UnionDevote");
        //     });
        // })
    },

    onClickInfo() {
        utils.utils.openPrefabView("union/UnionInfo", null,initializer.unionProxy.clubInfo);
    },

    _onClickShowInfo(id){
        if (null == localcache.getItem(localdb.table_item, id)) return;
            utils.utils.openPrefabView("ItemInfo", !1, {id:id, kind: bagProxy.DataType.ITEM});

    },

    onClickShowMemberInfo(){
        utils.alertUtil.alert(i18n.t("UNION_MEMBER"));
    },

    // updateCopyRed() {
    //     var t = !1,
    //     e = initializer.unionProxy.bossInfo;
    //     if (e && e.length > 0) for (var o = 0; o < e.length; o++) if (0 != e[o].id && 1 == e[o].type) {
    //         t = !0;
    //         break;
    //     }
    //     var l = utils.timeUtil.second > utils.timeUtil.getTodaySecond(12) || utils.timeUtil.second < utils.timeUtil.getTodaySecond(11.5);
    //     this.redCopy.active = t && l;
    // },

    updatefloat(dt){
        //UNION_MANAGER_DISMISSTIME
        if(!initializer.unionProxy.clubInfo || !initializer.unionProxy.clubInfo.dissolutionTime){
            return
        }
        this.updateIndex+=dt
        if(this.updateIndex<1){
            return
        }
        this.updateIndex -= 1 
        let strings = i18n.t("UNION_MANAGER_DISMISSTIME")
        let timem = initializer.unionProxy.clubInfo.dissolutionTime - utils.timeUtil.second
        if(timem < 0){
            //发送解散消息
            this.isUpdatefloat = false
            initializer.unionProxy.sureSendJiesan()
        }else{
            let h = utils.utils.fullZero(parseInt(timem/60/60),2)
            let s = utils.utils.fullZero(parseInt(timem%60),2) 
            let m = utils.utils.fullZero(parseInt((timem/60)%60),2)  
            strings += " "+h+":"+m+":"+s
            this.lbNotice.string = strings
        }
        
    },

    update(dt){
        this.runNoticeIndex(dt)
        if(this.isUpdatefloat){
            this.updatefloat(dt)
        }
    },

    runNoticeIndex(dt){
        this.lbNotice.node.x-=dt*40
        if(this.lbNotice.node.x < this.m_x-800-this.lbNotice.string.length*24-50){
            this.lbNotice.node.x = this.m_x
        }
    },

    /**点击退出*/
    onClickExit(){
        var self = this;
        utils.utils.showConfirm(i18n.t("UNION_TUI_CHU_TI_SHI"),
        function() {
            initializer.unionProxy.sendOut();
            self.eventClose();
        });
    },
});
