var i = require("Initializer");
var n = require("Utils");
var l = require("ItemSlotUI");
var r = require("UrlLoad");
var a = require("UIUtils");
var List = require("List");

cc.Class({
    extends: cc.Component,
    properties: {
        servantShow:r,
        itemRwd:l,
        btnQianDao:cc.Button,
        btnYiQianDao: cc.Button,
        lblTime: cc.Label,
        displayList: List,
        signedDaysLabel: cc.Label
    },
    ctor() {
        this.curItem = null;
        this.roleIndex = 0;
        this.roleList = [];
    },
    onLoad() {
        this.roleIndex = 0;
        facade.subscribe(i.thirtyDaysProxy.THIRTY_DAY_DATA_UPDATE, this.onUpdateShow, this);
        // facade.subscribe(i.thirtyDaysProxy.THIRTY_DAY_SHOW_DATA, this.showDisplayReward, this);
        i.thirtyDaysProxy.sendOpenActivity();
        this.onUpdateShow();

    },
    onUpdateShow() {
        var t = this,
        e = i.thirtyDaysProxy.data;
        if (e) {
            this.curItem = i.thirtyDaysProxy.getCurrentItem();
            // for (var k = 0; k < e.level.length; k++) {
            //     if(e.level[k].type != 2) {
            //         this.curItem = e.level[k];
            //         break;
            //     }
            // }


            // a.uiUtils.countDown(e.info.eTime, this.lblTime,
            // function() {
            //     n.timeUtil.second >= e.info.eTime && (t.lblTime.string = i18n.t("ACTHD_OVERDUE"));
            // });

            // 所有的已签完
            if (this.curItem == null) {
                this.curItem = e.level[e.level.length - 1];
            }
            this.itemRwd.data = e.rwd[this.curItem.day - 1].items[0];
            this.btnYiQianDao.node.active = !(this.btnQianDao.node.active = 1 == this.curItem.type);
            this.getRoles();
            this.setRoleShow(this.roleIndex);
            this.showDisplayReward();
            this.showSignedDay();
        }
    },

    showDisplayReward () {
        var e = i.thirtyDaysProxy.data;
        var display = [];
        var displayId = [2, 7, 14, 21, 28, 43];
        var count = 0;
        for (var j = 0; j < e.rwd.length; j++) {
            if (j + 1 === displayId[count]) {
                count++;
                display.push(e.rwd[j]);
            }
        }

        this.displayList.data = display;
    },

    showSignedDay () {
        var data = i.thirtyDaysProxy.data;
        var count = 0;
        for (var k = 0; k < data.level.length; k++) {
            if (data.level[k].type === 2) {
                count++;
            } else break;
        }
        this.signedDaysLabel.string = count;
    },

    setRoleShow(roleIndex) {
        // var e = this;
        // if (this.servantShow) {
        //     this.servantShow.loadHandle = function() {
        //         var t = e.servantShow.node.children[0];
        //         t && (t = t.children[0]) && (t.color = n.utils.BLACK);
        //     };
        //     var o = "";
        //     var i = t.rwd[6].items[0].id;
        //     i = 58;
        //     if (i > 200) {
        //         var l = localcache.getItem(localdb.table_wife, i - 200);
        //         l && l.res && (o = a.uiHelps.getWifeBody(l.res));
        //     } else o = a.uiHelps.getServantSpine(i);
        //     this.servantShow.url = o;
        // }
        if (!this.roleList.length) return;
        var item = this.roleList[roleIndex];
        var roleData = null;
        var roleRes = "";
        if (item.items[0].kind === 7) {
            // 伙伴
            roleData = localcache.getItem(localdb.table_hero, item.items[0].id);
            roleRes = a.uiHelps.getServantSpine(item.items[0].id);
        } else if (item.items[0].kind === 8) {
            // 知己
            roleData = localcache.getItem(localdb.table_wife, item.items[0].id);
            roleRes = a.uiHelps.getWifeBody(roleData.res);
        }
        this.servantShow.loadHandle = () => {
            this.roleIndex = roleIndex;
        }
        this.servantShow.url = roleRes;
    },

    getRoles () {
        this.roleList = [];
        var data = i.thirtyDaysProxy.data;
        if (!data) return;
        for (var k = 0; k < data.rwd.length; k++) {
            var item = data.rwd[k].items[0];
            if (item.kind === 7 || item.kind === 8) {
                this.roleList.push(data.rwd[k]);
            }
        }
    },

    onRoleTab () {
        var roleIndex = this.roleIndex + 1;
        if (roleIndex > this.roleList.length - 1) {
            roleIndex = 0;
        }
        this.setRoleShow(roleIndex);
    },

    onClickTab(t, e) {
        if (0 == e) {
            if (this.curItem &&  1 != this.curItem.type) {
                if (this.curItem.type == 0) {
                    n.alertUtil.alert18n("TIRTY_DAY_YI_QIAN_DAO");
                }
                return;
            }
            var rewardData = i.thirtyDaysProxy.data.rwd;
            if (this.curItem && rewardData) {
                var rewardData = i.thirtyDaysProxy.data.rwd;
                var rewardID = rewardData[this.curItem.day - 1].id;
                i.thirtyDaysProxy.sendGet(rewardID);
            }
        } else 1 == e && n.utils.openPrefabView("thirtydays/DailyCheckView");
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
