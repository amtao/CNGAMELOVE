var i = require("Utils");
var n = require("ItemSlotUI");
var l = require("BagProxy");
var List = require("List");
var Initializer = require("Initializer");
import { ITEM_GETTYPE } from "GameDefine";
cc.Class({
    extends: cc.Component,
    properties: {
        itemSlot: n,
        lblName: cc.Label,
        lblEffect: cc.Label,
        lblOut: cc.Label,
        lblCount: cc.Label,
        propNode:cc.Node,
        propInfo:cc.Label,
        lblTitles: [cc.Label],
        seColor: cc.Color,
        norColor: cc.Color,
        btns: [cc.Button],
        goItemList:List,
        lblNull:cc.Label,
        nBtnBgs: [cc.Node],
        nodenull:cc.Node,
        nodeClotheSuit:cc.Node,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        t ? this.showInfo(t) : this.onClickClose();
    },
    showInfo(t) {
        if (null != t) {
            this.itemSlot.data = t;
            this.nodeClotheSuit.active = false;
            var e = t.id ? t.id: t.itemid;
            switch (t.kind ? t.kind: 1) {
            case l.DataType.HEAD_BLANK:
                var o = localcache.getItem(localdb.table_userblank, e);
                this.lblName.string = o ? o.name: "";
                this.lblOut.string = o ? o.des: "";
                this.lblEffect.string = "";
                break;
            case l.DataType.CLOTHE:
                var n = localcache.getItem(localdb.table_userClothe, e);
                this.lblName.string = n.name;
                this.lblOut.string = n.text;
                this.lblEffect.string = n.des ? n.des : "";
                this.resetPropNodePos(t);
                break;
            case l.DataType.JB_ITEM:
                var r = localcache.getItem(localdb.table_heropve, e);
                this.lblName.string = r.name + i18n.t("WISHING_JB_JU_QING");
                this.lblEffect.string = r.msg;
                this.lblOut.string = 6 == r.unlocktype ? i18n.t("WISHING_GET_WAY_2") : i18n.t("WISHING_GET_WAY");
                break;
            case l.DataType.HERO_CLOTHE:
                var a = localcache.getItem(localdb.table_heroClothe, t.id);
                this.lblName.string = a.name;
                this.lblOut.string = a.way;
                this.lblEffect.string = a.txt;
                break;
            case l.DataType.CHENGHAO:
                var s = localcache.getItem(localdb.table_fashion, t.id);
                this.lblName.string = s ? s.name: "";
                this.lblOut.string = s ? s.des: "";
                this.lblEffect.string = "";
                break;
            case l.DataType.USER_JOB:
                var userTable = localcache.getItem(localdb.table_userjob, t.id);
                this.lblName.string = userTable ? userTable.name: "";
                this.lblOut.string = userTable && userTable.des ? userTable.des: "";
                this.lblEffect.string = "";
                break;
            case l.DataType.BAOWU_ITEM:
            case l.DataType.BAOWU_SUIPIAN:{
                    var cg = localcache.getItem(localdb.table_baowu, e);
                    if (cg){
                        this.lblName.string = cg.name;
                        this.lblEffect.string = cg.desc;
                    }
                    var c = localcache.getItem(localdb.table_item, cg.item);
                    this.lblOut.string = i.stringUtil.isBlank(c.source) ? i18n.t("COMMON_NULL") : c.source;
                    this.lblEffect.string = "";
                }
                break;
            case l.DataType.BUSINESS_ITEM:{
                    var cg = localcache.getItem(localdb.table_wupin, e);
                    if (cg){
                        this.lblName.string = cg.name;
                        this.lblEffect.string = i18n.t("BUSINESS_TIPS31");
                    }
                }            
                break;
            case l.DataType.FISHFOOD_ITEM:{
                    var cg = localcache.getItem(localdb.table_game_item, e);
                    if (cg){
                        this.lblName.string = cg.name;
                        this.lblEffect.string = cg.txt;
                    }
                }            
                break;
            case l.DataType.USER_SUIT:{
                    var cg = localcache.getItem(localdb.table_card, e);
                    if (cg) {
                        this.lblName.string = cg.name;
                        this.lblEffect.string = " ";
                    }
                }            
                break;
            default:               
                var c = localcache.getItem(localdb.table_item, e),
                _ = c.explain.split("|");
                this.lblName.string = c.name;
                this.lblEffect.string = _.length > 1 ? _[1] : c.explain;
                //this.lblOut.string = i.stringUtil.isBlank(c.source) ? i18n.t("COMMON_NULL") : c.source;            
            }
            this.lblCount && (this.lblCount.string = t.count && t.count > 1 ? i18n.t("COMMON_COUNT", {
                c: t.count
            }) : "");
            let openType = t.openType ? t.openType : 1;
            if (t.isActive){
                openType = 2;
            }
            this.onClickTab(null,openType);
        }
    },
    resetPropNodePos(itemInfo){
        if(this.propNode && this.propInfo){
            this.propInfo.node.width = 45;
            if(itemInfo.kind == l.DataType.CLOTHE){
                let prop = localcache.getItem(localdb.table_userClothe,itemInfo.id);
                if (1 != prop.prop_type){
                    this.propInfo.node.width = 110;
                }
            }
        }
    },
    onClickClose() {
        i.utils.closeView(this);
    },

    onClickTab(t, strIndex) {
        let index = parseInt(strIndex) - 1;
        for (let i = 0; i < this.btns.length; i++) {
            let bCur = index == i;
            this.btns[i].interactable = !bCur;
            this.nBtnBgs[i].active = bCur;
            this.lblTitles[i].node.color = bCur ? this.seColor: this.norColor;
        }
        this.refreshList(index);
    },

    refreshList(idx){
        var t = this.node.openParam;
        let cfg = null;
        var e = t.id ? t.id: t.itemid;
        switch (t.kind ? t.kind: 1) {
            case l.DataType.HEAD_BLANK:
                cfg = localcache.getItem(localdb.table_userblank, e);
                
                break;
            case l.DataType.CLOTHE:
                cfg = localcache.getItem(localdb.table_userClothe, e);
                this.nodeClotheSuit.active = t.showSuit != null && Initializer.playerProxy.isSuitPartById(e) != -1;
                break;
            case l.DataType.JB_ITEM:
                cfg = localcache.getItem(localdb.table_heropve, e);
                
                break;
            case l.DataType.HERO_CLOTHE:
                cfg = localcache.getItem(localdb.table_heroClothe, t.id);
                
                break;
            case l.DataType.CHENGHAO:
                cfg = localcache.getItem(localdb.table_fashion, t.id);
                
                break;
            case l.DataType.USER_JOB:
                cfg = localcache.getItem(localdb.table_userjob, t.id);
                
                break;
            case l.DataType.BAOWU_ITEM:{
                cfg = localcache.getItem(localdb.table_baowu, e);
                    
                }
                break;
            case l.DataType.BUSINESS_ITEM:{
                    cfg = localcache.getItem(localdb.table_wupin, e);
                }            
                break;
            case l.DataType.FISHFOOD_ITEM:{
                    cfg = localcache.getItem(localdb.table_game_item, e);
                }            
                break;
            default:               
                cfg = localcache.getItem(localdb.table_item, e);
                break;               
                         
        }
        let listdata = [];
        if (cfg == null) return;
        if (cfg.use == null){
            this.btns[1].node.active = false;
            if (idx == 1){
                this.btns[0].interactable = false;
                this.nBtnBgs[0].active = true;
                this.lblTitles[0].node.color = true ? this.seColor: this.norColor;
                idx = 0;
            }
        }
        else{
            this.btns[1].node.active = true;
        }
        let copyflag = false;
        if (idx == 0){
            if (cfg.iconopen){
                if (cfg.iconopen[0].type == ITEM_GETTYPE.READ_FIXTEXT){
                    this.goItemList.node.active = false;
                    this.nodenull.active = true;
                    this.lblNull.string = cfg.text ? cfg.text : cfg.txt;
                    return;
                }
                for (var ii = 0; ii < cfg.iconopen.length;ii++){
                    let iconopenCfg = localcache.getItem(localdb.table_iconOpen,cfg.iconopen[ii].score);
                    if (iconopenCfg == null || !i.utils.isOpenView(iconopenCfg.url)){
                        listdata.push({parm:cfg.iconopen[ii],id:e});
                    }
                    else{
                        copyflag = true;
                    }                 
                    
                }
            }          
        }
        else{
            if (cfg.use){
                for (var ii = 0; ii < cfg.use.length;ii++){
                   // listdata.push({parm:cfg.use[ii],id:e});
                    let iconopenCfg = localcache.getItem(localdb.table_iconOpen,cfg.use[ii].score);
                    if (iconopenCfg == null || !i.utils.isOpenView(iconopenCfg.url)){
                        listdata.push({parm:cfg.use[ii],id:e});
                    }
                    else{
                        copyflag = true;
                    }
                }
            }
        }
        this.goItemList.data = listdata;
        if (listdata.length == 0){
            this.lblNull.string = copyflag ? i18n.t("COMMON_NONEOTHERPATH") : i18n.t("COMMON_NONEPATH");
            this.nodenull.active = true;
        }
        else{
            this.nodenull.active = false;
        }
    },

    onClickSuit(){
        var t = this.node.openParam;
        var e = t.id ? t.id: t.itemid;
        switch (t.kind ? t.kind: 1) {
        case l.DataType.CLOTHE:
            let listcfg = localcache.getList(localdb.table_usersuit);
            for (var ii = 0; ii < listcfg.length; ii++){
                let cg = listcfg[ii];
                if (cg.clother.indexOf(e) != -1){
                    i.utils.openPrefabView("user/UserSuitDetail", !1, { data: {idx:cg.type,suitid:cg.id,clotheid:e}});
                    break;
                }
            }
            break;
        }
        this.onClickClose();
    },
});
