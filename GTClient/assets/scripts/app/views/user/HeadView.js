var i = require("UserHeadItem");
var n = require("List");
var l = require("Utils");
var r = require("Initializer");
var a = require("Config");
var UrlLoad = require("UrlLoad");

cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblDes: cc.Label,
        headItem: i,
        list: n,
        scroll: cc.ScrollView,
        nodeHead: cc.Node,
        nodeBlank: cc.Node,
        roleSpine: UrlLoad,
        head: cc.Node,
        seColor: cc.Color,
        nonColor: cc.Color,
    },

    ctor(){
        this._head = 0;
        this._blank = 1;
    },

    onLoad() {
        facade.subscribe(r.playerProxy.PLAYER_SHOW_CHANGE_UPDATE, this.updateRoleShow, this);
        facade.subscribe(r.playerProxy.PLAYER_UPDATE_HEAD, this.updateRoleShow, this);
        if (null != r.playerProxy.headavatar) {
            this._blank = r.playerProxy.headavatar.blank;
            var t = localcache.getItem(
                localdb.table_userhead,
                r.playerProxy.headavatar.head
            );
            this.lblDes.string = t && t.des ? t.des + "" : "";
            this.lblName.string = t && t.name ? t.name + "" : "";
            // this._head =
            //     0 == r.playerProxy.headavatar.head ||
            //     (t && 0 != t.job && t.job != r.playerProxy.userData.job)
            //         ? r.playerProxy.userData.job + 1e4
            //         : r.playerProxy.headavatar.head;
            this._head = r.playerProxy.headavatar.head;
        } else {
            this._head = r.playerProxy.userData.job + 1e4;
        }
        this.onClickHead(null, 1);
        this.roleSpine.node.active = r.playerProxy.headavatar.head == 0
        this.head.active = r.playerProxy.headavatar.head != 0
        r.playerProxy.loadPlayerSpinePrefab(this.roleSpine);
    },
    updateRoleShow() {
        this._head = r.playerProxy.headavatar.head
        this._blank = r.playerProxy.headavatar.blank
        this.roleSpine.node.active = this._head == 0;
        this.head.active = this._head != 0;
        this.headItem.setHead(this._head, this._blank);
        r.playerProxy.loadPlayerSpinePrefab(this.roleSpine);
    },
    onClickClost() {
        l.utils.closeView(this);
    },
    onClickOk() {
        // null != localcache.getItem(localdb.table_userhead, this._head) &&
        //     (r.playerProxy.isHaveBlank(this._blank)
        //         ? r.playerProxy.sendHeadBlank(this._head, this._blank)
        //         : l.alertUtil.alert18n("USER_UNHAVE_BLANK"));
        if(this._head == 0 && r.playerProxy.isHaveBlank(this._blank))
        {
            r.playerProxy.sendHeadBlank(this._head, this._blank)
        }else if(this._head != 0 && null != localcache.getItem(localdb.table_userhead, this._head) 
            && r.playerProxy.isHaveBlank(this._blank))
        {
                r.playerProxy.sendHeadBlank(this._head, this._blank)
        }
        else{
            l.alertUtil.alert18n("USER_UNHAVE_BLANK")
        }
    },
    onClickReverse(){
        r.playerProxy.sendHeadBlank(0, 1);
    },
    onClickHeadItem(t, e) {
        var o = e.data;
        this.lblDes.string = o.des ? o.des + "" : "";
        this.lblName.string = o.name + "";
        if (null != o.blankmodel) {
            this._blank = o.id;
        }
        else {
            this._head = o.id;
        }
        // if(this.nodeBlank.active == false) {
            // if(this._head != 0)
            // {
            //     this.roleSpine.node.active = false;
            //     this.head.active = true;
            // }
            // else{
            //     this.roleSpine.node.active = true;
            //     this.head.active = false;
            // }
            
            this.roleSpine.node.active = this._head == 0;
            this.head.active = this._head != 0;
        // } else {
        //     this.roleSpine.node.active = false;
        //     this.head.active = true;
        // }
        this.headItem.setHead(this._head, this._blank);
    },
    onClickHead(t, e) {
        var o = parseInt(e);
        this.nodeHead.color = -1 == o ? this.nonColor : this.seColor;
        this.nodeBlank.color = 1 == o ? this.nonColor : this.seColor;
        //this.lblDes.node.active = this.lblName.node.active = -1 == o;
        this.list.data = -1 == o ? this.getBlankList() : this.getHeadList();
        this.scroll.scrollToTop();
        if (-1 == o) {
            var i = localcache.getItem(
                localdb.table_userblank,
                this._blank
            );
            if (i) {
                this.lblDes.string = i.des + "";
                this.lblName.string = i.name + "";
            }
        } else {
            let headData = localcache.getItem(localdb.table_userhead, this._head);
            this.lblDes.string = headData && headData.des ? headData.des + "" : "";
            this.lblName.string = headData && headData.name ? headData.name + "" : "";
        }
    },
    getBlankList() {
        for (
            var t = localcache.getList(localdb.table_userblank),
                e = [],
                o = 0;
            o < t.length;
            o++
        ){
            if (0 != t[o].type){
                if (t[o].display == null || t[o].display.length == 0 || -1 != t[o].display.indexOf(a.Config.pf)){
                    e.push(t[o]);
                }
            }
        }
        return e;
    },
    getHeadList() {
        for (
            var t = localcache.getList(localdb.table_userhead),
                e = [{id:0,name:""}],
                o = r.playerProxy.userData.job,
                i = 0;
            i < t.length;
            i++
        )
            (0 != t[i].job && t[i].job != o) || e.push(t[i]);
        return e;
    },
});