var n = require("Utils");
var l = require("Initializer");
let scBuildInfoRender = require("BuildInfoRender");
let scGameChooseEventItem = require("GameChooseEventItem");
let scRwdInfoItem = require("scEventRwdRender");
import { MINIGAMETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        //list: i,
        lblBuildName: cc.Label,
        lbDesc: cc.Label,
        lblCost: cc.Label,
        nodeLook: cc.Node,
        btnLook: cc.Button,
        nParent: cc.Node,
        nBg: cc.Node,
        nBuildInfoItem: cc.Node,
        nEventInfoItem: cc.Node,
        RwdInfoItem: scRwdInfoItem,
    },

    ctor() {
        this._curBuiliId = 0;
    },

    onLoad() {
        var t = (this._curBuiliId = this.node.openParam);
        if (t) {
            var e = localcache.getGroup(localdb.table_lookCityEvent, "city", t);
            if (e == null) {
                this.onClickClose();
                return;
            }
            for (var o = [], i = 0; i < e.length; i++) {
                var n = !0;
                0 != e[i].disappear && (1 == e[i].disappear ? (n = l.lookProxy.isOpen(e[i].dp_para)) : 2 == e[i].disappear ? (n = l.taskProxy.mainTask.id < e[i].dp_para) : 3 == e[i].disappear ? (n = l.playerProxy.userData.bmap >= e[i].dp_para) : 4 == e[i].disappear && (n = l.playerProxy.userData.level >= e[i].dp_para));
                n && e[i].type != 1 && o.push(e[i]);
            }
            var r = localcache.getItem(localdb.table_lookBuild, t);
            this.lblBuildName.string = r.name;
            let eveData = localcache.getFilter(localdb.table_lookCityEvent, "type", 1, "city", r.id); 
            this.lbDesc.string = eveData.text;
            o.sort(this.sortList);

            let height = 150;    

            let nMemory = this.initNode(i18n.t("LOOK_MEMORY")); //回忆
            for(let i = 0, len = o.length; i < len; i++) {
                let nNodeBuild = cc.instantiate(this.nBuildInfoItem);
                nNodeBuild.parent = nMemory;
                nNodeBuild.active = true;
                let scBuildItem = nNodeBuild.getComponent(scBuildInfoRender);
                scBuildItem._data = o[i];
                scBuildItem.showData();
            }

            height += (70 + (o.length * 134));

            if(null != l.servantProxy.inviteEventData.events[t]) {
                this.nEvent = this.initNode(i18n.t("LOOK_EVENT")); //事件
                this.arrEvents = [];
                let eventData = l.servantProxy.inviteEventData.events[t];
                let count = 0;
                for (let key in eventData) {
                    count++;
                    let nNodeEvent = cc.instantiate(this.nEventInfoItem);
                    nNodeEvent.parent = this.nEvent;
                    nNodeEvent.active = true;
                    let scEventItem = nNodeEvent.getComponent(scGameChooseEventItem);
                    let type = key == "fish" ? MINIGAMETYPE.FISH : MINIGAMETYPE.FOOD;
                    scEventItem._data = { id: eventData[key], city: Number(t), type: type, heroid: null };
                    scEventItem.showData();
                    this.arrEvents.push(scEventItem);
                }
                height += (70 + (count * 134));
            }      

            this.RwdInfoItem.node.parent = this.nParent; //特产
            this.RwdInfoItem.node.active = true;
            height += (32 + this.RwdInfoItem.setData(t));

            this.nParent.height = height;

            //this.list.data = o;
            this.updateCost();
            this.nodeLook.active = l.playerProxy.userData.level > 5 && l.playerProxy.userData.bmap > r.lock;
        }
        facade.subscribe(l.lookProxy.UPDATE_XUNFANG_XFINFO, this.updateCost, this);
        facade.subscribe("UPDATE_CITY_INVITE_INFO", this.updateEvent, this);
    },

    updateEvent: function() {
        var cityId = this._curBuiliId;
        if (cityId && this.nEvent) {
            let eventData = l.servantProxy.inviteEventData.events[cityId];
            if(null == eventData) {
                this.nEvent.active = false;
            } else {
                this.arrEvents[0]._data = { id: eventData["fish"], city: Number(cityId), type: MINIGAMETYPE.FISH, heroid: null };
                this.arrEvents[0].showData();
                this.arrEvents[1]._data = { id: eventData["food"], city: Number(cityId), type: MINIGAMETYPE.FOOD, heroid: null };
                this.arrEvents[1].showData();
            }  
        }
    },

    initNode: function(name) {
        let nNode = cc.instantiate(this.nBg);
        nNode.parent = this.nParent;
        nNode.active = true;
        nNode.getComponentInChildren(cc.Label).string = name;
        return nNode;
    },

    updateCost() {
        this.lblCost.string = n.utils.formatMoney(this.getCost());
        this.btnLook.interactable = !0;
    },

    getCost() {
        return (n.utils.getParamInt("xunfang_city_jiage") + n.utils.getParamInt("xunfang_city_jiage_add") * (l.lookProxy.xfinfo.lastTime < n.timeUtil.getTodaySecond() ? 0 : null == l.lookProxy.xfinfo.count ? 0 : l.lookProxy.xfinfo.count));
    },

    onClickLook() {
        if (0 != this._curBuiliId) if (l.playerProxy.userData.cash < this.getCost()) n.alertUtil.alertItemLimit(1);
        else if (l.lookProxy.xfinfo.num <= 0) {
            var t = n.utils.getParamInt("xf_cost_item_tl"),
            e = l.bagProxy.getItemCount(t);
            n.utils.showConfirmItem(i18n.t("LOOK_USE_RECY_CONFIRM", {
                n: l.playerProxy.getKindIdName(1, t),
                c: 1
            }), t, e,
            function() {
                e < 1 ? n.alertUtil.alertItemLimit(t) : l.lookProxy.sendRecover();
            },
            "LOOK_USE_RECY_CONFIRM");
        } else {
            this.btnLook.interactable = !1;
            l.lookProxy.sendXunfan(100 + this._curBuiliId);
            this.onClickClose();
        }
    },

    sortList(t, e) {
        var o = l.lookProxy.isLock(t) ? 0 : 1,
        i = l.lookProxy.isLock(e) ? 0 : 1;
        return o > i ? 1 : o < i ? -1 : o == i ? t.id - e.id: -1;
    },

    onClickClose() {
        n.utils.closeView(this);
    },
});
