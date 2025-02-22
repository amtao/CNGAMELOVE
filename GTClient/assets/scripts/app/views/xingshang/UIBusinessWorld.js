var Utils = require("Utils");
var UIUtils = require("UIUtils");
var Initializer = require("Initializer");
var TimeProxy = require("TimeProxy");
cc.Class({
    extends: cc.Component,

    properties: {
        btnArr: [cc.Button],
        lblGoldleaf:cc.Label,
        lblGet:cc.Label,
        lblBagNum:cc.Label,
        nodeRed:cc.Node,
        lblChat: cc.Label,
        lblName: cc.Label,
        macheSpine:sp.Skeleton,
        nodeMache:cc.Node,
        nodeTag:cc.Node,
        scroll:cc.ScrollView,
        nodeMask:cc.Node,
        lblTicket:cc.Label,
        lblBusinessDes:cc.Label,
        lblRewardDes:cc.Label,
    },

    ctor() { 
        this.curMsg = null;
        this.dstPos = cc.v2(0,0);
        this.moveCity = 0;
        this.mFirst = true;
        this.lastGoldLeaf = 0;
        this.lastGetMoney = 0;
        this.offset2 = cc.v2(0,0);
        this.m_currentTime = 0;
        this.m_totalTime = 0;
        this.m_startPos = cc.v2(0,0);
        this.m_speedX = 0.0;
        this.m_speedY = 0.0;
        this.isMoving = false;
    },

    onLoad() {
        Initializer.businessProxy.businessStoryFinished = false;
        this.countDown();
        facade.subscribe("STORY_END", this.storyEnd, this);
        facade.subscribe("BUSINESS_UPDATEENTERNUM", this.onUpdateEnterNum, this);   
        facade.subscribe("BUSINESS_UPDATEINFO", this.initBuildInfo, this);   
        facade.subscribe("UPDATE_RAND_PRODUCT", this.countDown, this);
        facade.subscribe("BUSINESS_UPDATEBAGINFO", this.updateBagInfo, this);
        facade.subscribe("BUSINESS_CLICKCITY", this.playMacheAni, this);
        facade.subscribe("COMMON_CLOSE_VIEW",this.colseCommonView,this);
        facade.subscribe(Initializer.monthCardProxy.MOON_CARD_UPDATE, this.updateBagInfo, this);
        facade.subscribe(Initializer.chatProxy.UPDATE_CLUB_MSG, this.updateClubChat, this);
        facade.subscribe(Initializer.chatProxy.UPDATE_SYS_MSG, this.updateSysChat, this);
        facade.subscribe(Initializer.chatProxy.UPDATE_NOR_MSG, this.updateNorChat, this);    
        Initializer.businessProxy.sendGetInfo();
        this.updateNorChat(!1);
        this.updateSysChat(!0);
    },

    countDown: function() {
        let freshTime = Initializer.businessProxy.freshTime;
        if(!isNaN(freshTime)) {
            let targetTime = freshTime + (Utils.utils.getParamInt("xingshangshuaxin") * 60);
            UIUtils.uiUtils.countDown(targetTime, this, () => {
                cc.warn("refresh business get info");
                Initializer.businessProxy.sendGetInfo();
            });
        }
    },

    onUpdateEnterNum() {
        let data = Initializer.businessProxy.businessInfo;
        if (Initializer.businessProxy.isFirstEnter && Initializer.businessProxy.enterCount == 0) {
            //第一次进入行商，播放剧情
            let storyId = Utils.utils.getParamStr("xingshangkaitou");
            Initializer.playerProxy.addStoryId(storyId);
            Utils.utils.openPrefabView("StoryView");
        } else {
            Initializer.businessProxy.businessStoryFinished = true;
            if (data && data.isStart == 0 && !Initializer.businessProxy.isFinished) {
                if (!Utils.utils.isOpenView("xingshang/MerchantsView")) {
                    Utils.utils.openPrefabView("xingshang/MerchantsView");
                }
            }
        }
        Initializer.businessProxy.isFirstEnter = false;
    },
 
    /**刷新建筑数据*/
    initBuildInfo() {
        let data = Initializer.businessProxy.businessInfo;
        let citys = Initializer.businessProxy.businessInfo.unlockCity;
        for(let i = 0, len = this.btnArr.length; i < len; i++) {
            let city = this.btnArr[i];
            if(null != city) {
                let bHas = citys.filter((id) => {
                    return id == (i + 1);
                })
                city.node.active = bHas && bHas.length > 0;
                if (city.node.active){
                    let lblBtnTitle = city.getComponentInChildren(cc.Label);
                    let cfg = localcache.getItem(localdb.table_chengshi,i+1);
                    if (cfg && lblBtnTitle){
                        lblBtnTitle.string = cfg.chengshi;
                    }
                }
            }
        }
        this.lblGoldleaf.string = "：" + data.goldLeaf;
        this.lblTicket.string = "：" + data.AgTicket
        let getNum = Initializer.businessProxy.getCurLeafNum();
        if (getNum >= 0){
            this.lblBusinessDes.string = i18n.t("BUSINESS_TIPS18")
            this.lblGet.string = `${getNum}`;
        }
        else{
            this.lblBusinessDes.string = i18n.t("BUSINESS_TIPS26")
            this.lblGet.string = `${Math.abs(getNum)}`
        }

        this.lblRewardDes.string = Initializer.businessProxy.getRewardLevelDes();
        
        this.nodeRed.active = (data.AgTicket <= 0 || getNum >= Initializer.businessProxy.getBusinessMaxGoal());
        if (data.isStart == 0 && Initializer.businessProxy.enterCount > 0 && !Initializer.businessProxy.isFinished) {
            if (!Utils.utils.isOpenView("xingshang/MerchantsView")){
                Utils.utils.openPrefabView("xingshang/MerchantsView");
            }
        }

        if (this.mFirst || data.isStart == 0){
            this.nodeMache.position = this.btnArr[data.currentCity-1].node.position;
            let worldPos = this.nodeTag.convertToWorldSpaceAR(cc.Vec2.ZERO);
            let localPos = this.nodeMache.parent.convertToNodeSpaceAR(worldPos);
            let scrollContent = this.scroll.content;
            let contentPos = scrollContent.position;
            let offset = cc.v2(contentPos.x - this.nodeMache.position.x + localPos.x,contentPos.y - this.nodeMache.position.y + localPos.y);

            let maxX = this.nodeMask.width * (-0.5);
            let minX = (scrollContent.width + maxX) * (-1);
            let minY = (scrollContent.height - this.nodeMask.height) * (-0.5)
            let maxY = (scrollContent.height - this.nodeMask.height) * (0.5)
            if (offset.x < minX){
                offset.x = minX;
            }
            if (offset.x > maxX){
                offset.x = maxX;
            }
            if (offset.y < minY){
                offset.y = minY;
            }
            if (offset.y > maxY){
                offset.y = maxY;
            }
            this.scroll.content.position = offset;
        }
        this.mFirst = false;
    },

    /**刷新背包数据*/
    updateBagInfo(){
        let data = Initializer.businessProxy.bagInfo;
        if (data == null) return;
        let count = 0;
        if (data.totalCount != null){
            count = data.totalCount;
        }
        let buyInfo = Initializer.monthCardProxy.getCardData(1);
        let maxCount = Utils.utils.getParamInt("xingshang_beibao");
        if (buyInfo != null && buyInfo.type != 0){
            maxCount = Utils.utils.getParamInt("xingshang_beibao_yueka");
        }
        this.lblBagNum.string = i18n.t("BUSINESS_TIPS5") + count + "/" + maxCount;
    },

    /**进入第一次剧情播放结束*/
    storyEnd() {
        Utils.utils.openPrefabView("xingshang/MerchantsView");
        Initializer.businessProxy.businessStoryFinished = true;
    },

    onClickClost() {
        Utils.utils.closeView(this, !0);
    },
    
    /**点击建筑*/
    onClickBuild(event, param) {
        let data = Initializer.businessProxy.businessInfo;
        if (data.AgTicket <= 0){
            Utils.alertUtil.alert18n("BUSINESS_TIPS23");           
            return;
        }       
        if (Number(param) == Initializer.businessProxy.businessInfo.currentCity){
            Utils.utils.openPrefabView("xingshang/UIBusinessCityInfo", null, {idx: param});
            return;
        }
        Utils.utils.openPrefabView("xingshang/BusinessCityView", !1, {idx: param});
    },


    playMacheAni(param){      
        this.dstPos = this.btnArr[Number(param)-1].node.position;
        this.scroll.stopAutoScroll();
        this.changeCamera();
        this.moveCity = Number(param);
        this.nodeMache.stopAllActions();       
        let srcPos = this.nodeMache.position;
        this.nodeMache.scaleX = (this.dstPos.x > srcPos.x) ? -1 : 1;
        this.macheSpine.animation = this.getMacheAniName(this.getMacheDir(srcPos));
        let distance = this.dstPos.sub(srcPos).mag();
        let dt = distance / 300;  
        this.nodeMache.runAction(cc.sequence(cc.moveTo(dt,cc.v2(this.dstPos.x,this.dstPos.y)),cc.callFunc(()=>{
            Utils.utils.openPrefabView("xingshang/UIBusinessCityInfo", null, {idx: param});
        })))
        // this.scroll.content.runAction(cc.sequence(cc.moveTo(dt,this.offset2),cc.callFunc(()=>{
            
        // })))
        //this.scroll.scrollToOffset(this.offset2,dt)
        
        this.m_currentTime = 0;
        this.m_totalTime = dt;
        this.m_startPos = this.scroll.content.position;
        this.m_speedX = (this.offset2.x - this.m_startPos.x)/dt;
        this.m_speedY = (this.offset2.y - this.m_startPos.y)/dt;
        this.isMoving = true;
    },

    update(dt){
        if (!this.isMoving) return;
        this.m_currentTime += dt;
        if (this.m_currentTime >= this.m_totalTime){
            this.isMoving = false;
            this.scroll.content.position = this.offset2;
            return;
        }
        this.scroll.content.position = cc.v2(this.m_startPos.x +this.m_speedX * this.m_currentTime,this.m_startPos.y + this.m_speedY * this.m_currentTime);
    },

    changeCamera(){
        let worldPos = this.nodeTag.convertToWorldSpaceAR(cc.Vec2.ZERO);
        let localPos = this.nodeMache.parent.convertToNodeSpaceAR(worldPos);
        let scrollContent = this.scroll.content;
        let contentPos = scrollContent.position;
        let offset = cc.v2(contentPos.x - this.nodeMache.position.x + localPos.x,contentPos.y - this.nodeMache.position.y + localPos.y);

        let maxX = this.nodeMask.width * (-0.5);
        let minX = (scrollContent.width + maxX) * (-1);
        let minY = (scrollContent.height - this.nodeMask.height) * (-0.5)
        let maxY = (scrollContent.height - this.nodeMask.height) * (0.5)
        if (offset.x < minX){
            offset.x = minX;
        }
        if (offset.x > maxX){
            offset.x = maxX;
        }
        if (offset.y < minY){
            offset.y = minY;
        }
        if (offset.y > maxY){
            offset.y = maxY;
        }
        this.scroll.content.position = offset;
        localPos = this.nodeMache.parent.convertToNodeSpaceAR(worldPos);
        let offset2 = cc.v2(offset.x - this.dstPos.x + localPos.x,offset.y - this.dstPos.y + localPos.y);
        if (offset2.x < minX){
            offset2.x = minX;
        }
        if (offset2.x > maxX){
            offset2.x = maxX;
        }
        if (offset2.y < minY){
            offset2.y = minY;
        }
        if (offset2.y > maxY){
            offset2.y = maxY;
        }
        this.offset2 = offset2;
    },

    /**点击背包*/
    onClickBag(){
        Utils.utils.openPrefabView("xingshang/BusinessBagView");
    },

    /**订单内容*/
    onClickOrderDetail() {
        Utils.utils.openPrefabView("xingshang/BusinessOrderView");
    },

    /**打开聊天*/
    onClickChat() {
        TimeProxy.funUtils.isOpenFun(TimeProxy.funUtils.chatView) ? (this.curMsg && this.curMsg.type, Utils.utils.openPrefabView("chat/ChatView")) : TimeProxy.funUtils.openView(TimeProxy.funUtils.chatView.id);
    },

    updateNorChat(t) {
        void 0 === t && (t = !0);
        this.setShowChat(Initializer.chatProxy.getLastMsg(Initializer.chatProxy.norMsg));
    },
    updateClubChat(t) {
        void 0 === t && (t = !0);
        this.setShowChat(Initializer.chatProxy.getLastMsg(Initializer.chatProxy.clubMsg));
    },
    updateSysChat(t) {
        void 0 === t && (t = !0);
        Initializer.chatProxy.sysMsg && this.setShowChat(Initializer.chatProxy.getLastMsg(Initializer.chatProxy.sysMsg));
    },

    setShowChat(t) {
        this.curMsg = t;
        this.lblName.string = t ? i18n.t("chat_home_show", {
            name: t.user ? t.user.name: i18n.t("CHAT_SYS_TIP")
        }) : "";
        this.lblChat.string = t ? Initializer.chatProxy.getSpMsg(t.msg) : "";
        let chatMsg = this.lblChat.string;
        let chatMsgStr = ""
        for(let i = 0; i < chatMsg.length; i++)
        {
            chatMsgStr = chatMsgStr + (chatMsg[i]);
            this.lblChat.string = chatMsgStr;
            this.lblChat._forceUpdateRenderData();//强制刷新
            let width = this.lblChat.node.getContentSize().width;
            if(width > 360)
            {
                chatMsgStr = chatMsgStr + "..."
                break;
            }
        }
        chatMsg = chatMsgStr;
        this.lblChat.string = chatMsg;
    },

    getMacheDir(srcPos){
        if (Math.abs(this.dstPos.x - srcPos.x) < 20){
            return (this.dstPos.y > srcPos.y) ? 5 : 1;
        }
        else if(Math.abs(this.dstPos.y - srcPos.y) < 20){
            return 3;
        }
        else{
            return (this.dstPos.y > srcPos.y) ? 4 : 2;
        }
    },

    getMacheAniName(dir){
        let ani = "run1";
        switch(dir){
            /**方向竖直向下*/
            case 1:
                ani = "run1"
            break;
            /**方向左下*/
            case 2:
                ani = "run2"
            break;
            /**方向水平向左*/
            case 3:
                ani = "run3"
            break;
            /**方向左上*/
            case 4:
                ani = "run4"
            break;
            /**方向向上*/
            case 5:
                ani = "run5"
            break;
        }
        return ani;
    },

    onDestroy(){
        Initializer.businessProxy.isFirstEnter = true;
    },

    /**显示货币滚动效果**/
    showLabelAni(){
        let curGoldLeafNum = Initializer.businessProxy.businessInfo.goldLeaf;
        UIUtils.uiUtils.showNumChange(this.lblGoldleaf, this.lastGoldLeaf, curGoldLeafNum);
        this.lastGoldLeaf = curGoldLeafNum + 0;
        let curNum = Initializer.businessProxy.getCurLeafNum();
        UIUtils.uiUtils.showNumChange(this.lblGet, this.lastGetMoney, Math.abs(curNum));
        this.lastGetMoney = Math.abs(curNum) + 0;
    },

    colseCommonView(){
        if (this.node == null) return;
        let midLayer = cc.find("Canvas/midLayer");
        if (midLayer.childrenCount > 0){
            let child = midLayer.children[midLayer.childrenCount-1];
            if (child.name == this.node.name){
                this.showLabelAni();
            }
        }
    },

    onClickHelp(){
        Utils.utils.openPrefabView("xingshang/BusinessHelpView");
    },
});
