var i = require("Utils");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
var a = require("List");

cc.Class({
    extends: cc.Component,
    properties: {
        lblBall:cc.Label,
        lblJindu:cc.Label,
        lblLevel:cc.Label,
        lblTime:cc.Label,
        tipNode:cc.Node,
        record:a,
        scroll:cc.ScrollView,
        springEff_1:sp.Skeleton,
        springEff_10:sp.Skeleton,
        redNode:cc.Node,
        redNode2:cc.Node,
        aniBall:cc.Node,
    },

    ctor(){
        this.snowNum = 0;
        this.snowing = false;
        this.curIndex = 0;
    },

    onLoad() {
        facade.subscribe(l.christmasProxy.CHRISTMAS_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        facade.subscribe(l.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        facade.subscribe(l.christmasProxy.SNOWMAN_RECORDS_UPDATE, this.onRecord, this);
        facade.subscribe("LIMIT_ACTIVITY_HUO_DONG_LIST", this.onHuoDongRed, this);
        l.christmasProxy.sendOpenChristmasMan();
        l.shopProxy.sendList(!1);
        this.onItemUpdate();
        this.onHuoDongRed();
    },

    onHuoDongRed(){
        this.setLimitActivityRed();
    },

    setLimitActivityRed(){
        let actType = l.limitActivityProxy.CHRISTMAS_TYPE
        let actData = l.limitActivityProxy.getHuodongList(actType);
        let isHasNew = false
        for(let i = 0;i < actData.length;i++)
        {
            let news = actData[i].news;
            if(news == 1)
            {
                isHasNew = true;
                break;
            }
        }
        this.redNode.active = isHasNew;

        let isHasNew2 = false;
        let cons = l.christmasProxy.data && l.christmasProxy.data.cons || 0;
        let rwd = l.christmasProxy.data && l.christmasProxy.data.rwd || [];
        for(let i = 0;i < rwd.length;i++)
        {
            let get = rwd[i].get;
            let subCons = rwd[i].cons;
            if(get == 0 && cons >= subCons)
            {
                isHasNew2 = true;
                break;
            }
        }
        this.redNode2.active = isHasNew2;
    },

    onDataUpdate() {
        var t = this,
        e = l.christmasProxy.data;
        if (e) {
            r.uiUtils.countDown(e.info.eTime, this.lblTime,
            function() {
                i.timeUtil.second >= e.info.eTime && (t.lblTime.string = i18n.t("ACTHD_OVERDUE"));
            });

            this.lblBall.string = l.bagProxy.getItemCount(l.christmasProxy.data.need) + "";
        }
        this.setLimitActivityRed();
    },

    onItemUpdate() {
        if (l.christmasProxy.data) {
            var t = l.bagProxy.getItemCount(l.christmasProxy.data.need);
            this.lblBall.string = t + "";
        }
    },

    onClickSnow(t, e) {
        if (null != l.christmasProxy.data) if (this.snowing) i.alertUtil.alert18n("SNOWMAN_BUILDING");
        else {
            this.snowNum = parseInt(e);
            if (l.bagProxy.getItemCount(l.christmasProxy.data.need) >= this.snowNum) {
                if (1 == this.snowNum) {
                    if (1 == l.christmasProxy.data.info.hdtype) {
                        let aniNode = this.aniBall.getComponent(cc.Animation);
                        aniNode.play("xmasball");
                    }
                    this.scheduleOnce(this.onTimer, 1);
                } else if (10 == this.snowNum) if (1 == l.christmasProxy.data.info.hdtype) 
                {
                    let aniNode = this.aniBall.getComponent(cc.Animation);
                    aniNode.play("xmasball");
                    this.schedule(this.onShowEff, 1);
                }
                this.snowing = !0;
            }else{
                this.onClickAdd();
            }
        }
    },

    onTimer() {
        1 == this.snowNum && l.bagProxy.getItemCount(l.christmasProxy.data.need) >= 1 && l.christmasProxy.sendSnowManOnce();
        this.snowing = !1;
    },

    onShowEff() {
        this.snowing = !1;
        l.christmasProxy.sendSnowManTen();
        this.unscheduleAllCallbacks();
    },

    onTimer2() {
        this.snowing = !1;
        l.christmasProxy.sendSnowManTen();
    },

    onClickGift() {
        i.utils.openPrefabView("christmas/ChristmasReward");
    },

    onClickTab(t, e) {
        if ("0" == e) i.utils.openPrefabView("wishingwell/WishingActivityShopView", null, l.christmasProxy.dhShop, null, true);
        else if ("1" == e) {
            var o = 1 == l.christmasProxy.data.info.hdtype ? l.limitActivityProxy.CHRISTMAS_TYPE: l.limitActivityProxy.SPRING_TYPE;
            i.utils.openPrefabView("limitactivity/LimitActivityView", null, {
                type: o
            });
        }
    },

    onClickAdd() {
        i.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: l.christmasProxy.shop[0],
            activityId: l.christmasProxy.data.info.id
        });
    },

    onClickClose() {
        i.utils.closeView(this);
    },

    onRecord() {
        if (this.record) {
            this.record.data = l.christmasProxy.records;
            this.scroll.scrollToBottom();
        }
    },

    onClickRankRwd () {
        i.utils.openPrefabView("christmas/ChristmasRankRwd");
    }
});
