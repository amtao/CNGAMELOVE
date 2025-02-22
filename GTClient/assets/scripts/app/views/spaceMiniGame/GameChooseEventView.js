let Initializer = require("Initializer");
var List = require("List");
var Utils = require("Utils");
import { MINIGAMETYPE } from "GameDefine";
cc.Class({
    extends: cc.Component,

    properties: {
        listView:List,
        lblTitle:cc.Label,
    },

    onLoad: function() {
        var type = this.node.openParam.type;
        var heroid = this.node.openParam.heroid;
        let data = Initializer.servantProxy.inviteEventData.events;
        if (type == MINIGAMETYPE.FISH){
            this.lblTitle.string = i18n.t("FISH_TIPS13");
        }
        else if(type == MINIGAMETYPE.FOOD){
            this.lblTitle.string = i18n.t("SMG_FOOD");
        }
        let listdata = [];
        for (let key in data){
            let cg = data[key];
            if (type == MINIGAMETYPE.FISH){
                listdata.push({id:cg.fish,city:Number(key),type:type,heroid:heroid})
            }
            else{
                listdata.push({id:cg.food,city:Number(key),type:type,heroid:heroid})
            }
        }
        listdata = this.sortFunc(listdata);
        this.listView.data = listdata;
    },

    sortFunc: function(listdata) {
        let limitArr = listdata.filter((data) => {
            let cfg = localcache.getItem(localdb.table_games, data.id);
            return cfg.type == 2 || cfg.type == 4;
        });
        let noLimit = listdata.filter((data)=> {
            let cfg = localcache.getItem(localdb.table_games, data.id);
            return cfg.type == 1 || cfg.type == 3;
        });
        let finished = limitArr.filter((data) => {
            let cfg = localcache.getItem(localdb.table_games, data.id);
            let endTime = Initializer.servantProxy.inviteEventData.refreshTime + Number(cfg.start);
            return Utils.timeUtil.getCurSceond() >= endTime || (null != Initializer.servantProxy.inviteEventData.joinLimitEvent[data.city]
                && null != Initializer.servantProxy.inviteEventData.joinLimitEvent[data.city][data.id]);
        });
        let notFinished = limitArr.filter((data) => {
            let cfg = localcache.getItem(localdb.table_games, data.id);
            let endTime = Initializer.servantProxy.inviteEventData.refreshTime + Number(cfg.start);
            return Utils.timeUtil.getCurSceond() < endTime && (null == Initializer.servantProxy.inviteEventData.joinLimitEvent[data.city]
                || null == Initializer.servantProxy.inviteEventData.joinLimitEvent[data.city][data.id]);
        });
        noLimit = noLimit.sort((a, b) => {
            let i = localcache.getItem(localdb.table_games, a.id);
            let j = localcache.getItem(localdb.table_games, b.id);
            if(i.star > j.star) {
                return i;
            } else if(i.star < j.star) {
                return j;
            } else if(i.id > j.id) {
                return i;
            } else {
                return j;
            }
        });
        notFinished = notFinished.concat(noLimit);
        notFinished = notFinished.concat(finished);
        return notFinished;
    },

    //关闭
    onClickClose: function() {
        Utils.utils.closeView(this, !0);
    }, 

});
