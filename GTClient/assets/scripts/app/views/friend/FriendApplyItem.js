var renderListItem = require("RenderListItem");
var userHeadItem = require("UserHeadItem");
var initializer = require("Initializer");
cc.Class({
    extends: renderListItem,

    properties: {
        friendName : cc.Label,
        friendPower : cc.Label,
        friendVip : cc.Label,
        userHead : userHeadItem
    },
    onLoad () {

    },

    start () {

    },
    showData : function () {
        var d = this._data;
        this.uid = d.uid;

        this.friendName.string = d.name
        this.friendPower.string = i18n.t("FRIEND_APPLY_SHILI", {num: d.shili});
        this.friendVip.string = i18n.t("FRIEND_APPLY_VIP", {num: d.vip});

        this.userHead.setUserHead(d.job,d.headavatar);
    },
    
    onClickFriendHead : function(){
        if (!!this._data) {
            initializer.friendProxy.sendGetOther(this._data.uid);
        }
        // // var friend = initializer.friendProxy.getFriendById(this.uid);
        // // if(friend){
        //     var p = new proto_cs.user.getFuserMember();
        //     p.id = this.uid;
        //     JsonHttp.send(p, function (data) {
        //         utils.utils.openPrefabView("friend/FriendInfo",false,data.a.user.fuser);
        //     });
        // // }
    },

    touchAdd : function(){
        var p = new proto_cs.friends.fok();
        p.fuid = this.uid
        JsonHttp.send(p,function(){});
    },
    touchDel : function(){
        var p = new proto_cs.friends.fno();
        p.fuid = this.uid
        JsonHttp.send(p,function(){});
    },
});
