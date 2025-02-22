
let initializer = require("Initializer");
let utils = require("Utils");
let uiUtils = require("UIUtils");
let playerProxy = require("PlayerProxy");
let scStarShow = require("ServantStarShow");
let urlLoad = require("UrlLoad");
let itemSlot = require("ItemSlotUI");
let scList = require("List");

cc.Class({
    extends: cc.Component,

    properties: {
        // lblYueli: cc.Label,
        // lblGold: cc.Label,

        lbName: cc.Label,
        starShow: scStarShow,
        role: urlLoad,

        nRes: cc.Node,
        nInfo: cc.Node,

        nTgTexts: [cc.Node],
        seColor: cc.Color,
        norColor: cc.Color,

        nProp: cc.Node,
        lbIntro: cc.Label,
        lblTotal: cc.Label,
        lbQishi: cc.Label,
        lbGonglue: cc.Label,
        lbZhimou: cc.Label,
        lbMeili: cc.Label,

        item: itemSlot,
        lbItemName: cc.Label,
        lbGetWay: cc.Label,
        nGetWay: cc.Node,
        nItem: cc.Node,
        nInfo2: cc.Node,
        nAllIntro: cc.Node,
        lbAllIntro: cc.Label,

        list: scList,
        nTablent: cc.Node,
        lbZz: cc.Label,
        nBtnGet: cc.Node,
    },

    ctor: function() {
        //this.lastData = new playerProxy.RoleData();
        this.obj = { p1: 0, p2: 0, p3: 0, p4: 0 };
        this.bShow = false;
    },

    onLoad: function() {
        facade.subscribe("servantClose", this.onClickBack, this);
        //facade.subscribe(initializer.playerProxy.PLAYER_USER_UPDATE, this.onResUpdate, this);
        facade.subscribe(initializer.limitActivityProxy.UPDATE_DUIHUAN_HUODONG, this.handleItem, this);
        facade.subscribe("SERVANT_UP", this.showHasView, this);
        initializer.servantProxy.sortServantList();
        this.defaultRightY = this.role.node.position.y;
        this._data = this.node.openParam;
        this.showData(this.bShow);
        this.onToggleValueChange(null, 0);

        //this.onResUpdate();
    },

    showData: function(bShow) {
        this.bShow = bShow;
        
        this.nAllIntro.active = bShow;
        this.nRes.active = !bShow;
        this.nInfo.active = !bShow;
        this.nInfo2.active = !bShow;

        let data = this._data;
        this.lbName.string = data.name;
        this.starShow.setValue(data.star);

        this.role.loadHandle = () => {
            this.servantAnchorYPos(this.role);              
        };

        this.role.url = uiUtils.uiHelps.getServantSpine(data.heroid);
        let props = [];
        let val = 0;
        for (let i = 0; i < data.skills.length; i++) {
            let skillData = localcache.getItem(
                localdb.table_epSkill,
                data.skills[i].id
            );
            this.obj["p" + skillData.ep] += 10 * skillData.star;
            val += skillData.star;

            var prop = {};
            prop.id = skillData.sid;
            prop.level = prop.hlv = 0;
            props.push(prop);
        }
        this.list.data = props;
        this.lblTotal.string = val;
        this.lbZz.string = i18n.t("SERVANT_PROP_TOTAL", { value: val });
        this.lbQishi.string = this.obj.p1 + "";
        this.lbGonglue.string = this.obj.p2 + "";
        this.lbZhimou.string = this.obj.p3 + "";
        this.lbMeili.string = this.obj.p4 + "";
        
        this.lbIntro.string = data.txt;
        this.lbAllIntro.string = data.txt;

        this.getWay = data.zhaomu;
        this.bCanZhaomu = this.getWay == "1";
        this.nItem.active = false;
        this.nGetWay.active = !this.bCanZhaomu;
        this.lbGetWay.string = data.unlock;
        null == initializer.limitActivityProxy.duihuan ? initializer.limitActivityProxy.sendLookActivityData(initializer.limitActivityProxy.DUIHUAN_ID) : this.handleItem();
        this.nBtnGet.active = this.getWay != "0";
        console.error(this.getWay);
    },

    servantAnchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultRightY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        } 
    },

    handleItem: function() {
        if(!this.bCanZhaomu) 
            return;
        this.nItem.active = true;
        let heroData = this._data;
        let rwd = initializer.limitActivityProxy.duihuan.rwd;
        this.changeData = null;
        for(let i = 0, len = rwd.length; i < len; i++) {
            let tmpData = rwd[i];
            if(heroData.heroid == tmpData.heroid) {
                this.changeData = tmpData;
                break;
            }
        }
        let cfgData = localcache.getItem(localdb.table_item, this.changeData.itemid);
        this.lbItemName.string = cfgData.name;
        let itemData = { id: this.changeData.itemid, kind: cfgData.kind, count: this.changeData.need };
        this.item._data = itemData;
        this.item.showData();
        this.item.lblcount.string = i18n.t("COMMON_NUM", { f: initializer.bagProxy.getItemCount(this.changeData.itemid), s: this.changeData.need });
    },

    // onResUpdate() {
    //     uiUtils.uiUtils.showNumChange(this.lblYueli, this.lastData.coin, initializer.playerProxy.userData.coin);
    //     uiUtils.uiUtils.showNumChange(this.lblGold, this.lastData.cash, initializer.playerProxy.userData.cash);
    //     this.lastData.coin = initializer.playerProxy.userData.coin;
    //     this.lastData.cash = initializer.playerProxy.userData.cash;
    // },

    onToggleValueChange: function(toggle, index) {
        index = parseInt(index);

        for (var i = 0; i < this.nTgTexts.length; i++) {
            this.nTgTexts[i].color = index == i ? this.seColor: this.norColor;
        }
        this.lbIntro.node.active = index != 1;
        this.nProp.active = index == 1;
    },

    onClickBack: function() {
        if(this.bShow) {
            this.showData(false);
        } else {
            initializer.servantProxy.curSelectId = 0;
            utils.audioManager.playSound("", !0);
            initializer.servantProxy.isRenMaiOpen ? (initializer.servantProxy.isRenMaiOpen = !1) : utils.utils.openPrefabView("servant/ServantLobbyViewNew");
            utils.utils.closeView(this);
        }
    },

    onClickCheck: function() {
        this.nTablent.active = true;
    },

    onClickGet: function() {
        let getWayData = this.getWay.split(',');
        //console.error(getWay);
        let getWay = parseInt(getWayData[0]);
        switch(getWay) {
            case 1: {
                let data = this.changeData;
                if (data) {
                    if (initializer.bagProxy.getItemCount(data.itemid) < data.need) {
                        utils.alertUtil.alertItemLimit(data.itemid);
                        return;
                    }
                    initializer.limitActivityProxy.sendGetActivityReward(initializer.limitActivityProxy.DUIHUAN_ID, data.heroid);
                }
            } break;
            case 2: {
                utils.utils.openPrefabView("seriesFirstCharge/seriesFirstCharge");
            } break;
            case 3: {
                utils.utils.openPrefabView("welfare/RechargeView", !1, { type: getWay, value: parseInt(getWayData[1])});
            } break;
        }
    },

    onClickShowAll: function() {
        this.showData(true);
    },

    onClickCloseTablent: function() {
        this.nTablent.active = false;
    },

    showHasView: function() {
        utils.utils.openPrefabView("servant/ServantView", !1, {
            hero: this._data,
            tab: 4
        });
        utils.utils.closeView(this);
    },

});
