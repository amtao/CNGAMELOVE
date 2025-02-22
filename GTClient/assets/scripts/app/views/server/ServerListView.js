var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var r = require("Config");

cc.Class({
    extends: cc.Component,
    properties: {
        //lastListlastList: i,
        list: i,
        quList: i,
        //roleList: i,
        //serverNode: cc.Node,
        //roleNode: cc.Node,
        //lblList: cc.Label,
        //lblRole: cc.Label,
        //listImg: cc.Sprite,
        //roleImg: cc.Sprite,
        norColor: cc.Color,
        selColor: cc.Color,
        //btns: [cc.Button],
        //selectImg: cc.SpriteFrame,
        //btnNode: cc.Node,
        //oldServerNode: cc.Node,
        //nRoleBg: cc.Node, 
    },
    ctor() {},
    onLoad() {
        facade.subscribe(n.loginProxy.LOGIN_SERVER_LIST, this.update_ServerList, this);
        //facade.subscribe(n.loginProxy.LOGIN_ROLE_LIST, this.onRoleList, this);
        this.update_ServerList();
        //this.onRoleList();
        //this.onClickTab(null, "0");
        //this.btnNode.active = r.Config.isShowMyServer;
        //this.oldServerNode.active = !r.Config.isShowMyServer;
        //this.serverNode.y = r.Config.isShowMyServer ? 100 : 50;
    },
    update_ServerList() {
        var t = this;
        this.quList.data = n.loginProxy.quList;
        this.quList.selectHandle = function(e) {
            t.list.data = e ? e.list: null;
        };
        this.quList.selectIndex = 0;
        //this.lastList.data = n.loginProxy.lastList;
    },
    closeBtn() {
        l.utils.closeView(this);
    },
    clickItem(t, e) {
        var o = e.data;
        if (o) {
            n.loginProxy.pick(o.id);
            this.closeBtn();
        }
    },
    // onClickTab(t, e) {
    //     for (var o = 0; o < this.btns.length; o++) this.btns[o].interactable = o != parseInt(e);
    //     this.lblList.node.color = "0" == e ? this.selColor: this.norColor;
    //     this.lblRole.node.color = "1" == e ? this.selColor: this.norColor;
    //     this.serverNode.active = this.listImg.node.active = "0" == e;
    //     //this.roleNode.active = this.roleImg.node.active = this.nRoleBg.active = "1" == e;
    // },
    onClickRoleServer(t, e) {
        var o = e.data;
        if (o) {
            n.loginProxy.pick(o.serverid);
            this.closeBtn();
        }
    },
    // onRoleList() {
    //     if (null != n.loginProxy.roleList) {
    //         n.loginProxy.roleList.sort(function(t, e) {
    //             return e.lastlogin - t.lastlogin;
    //         });
    //         this.roleList.data = n.loginProxy.roleList;
    //     }
    // },
});
