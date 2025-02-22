var renderListItem = require("RenderListItem");
var userHeadItem = require("UserHeadItem");
var initializer = require("Initializer");
var utils = require("Utils");
cc.Class({
    extends: renderListItem,

    properties: {
        friendName : cc.Label,
        shili : cc.Label,
        vip : cc.Label,
        userHead : userHeadItem,
    },

    onLoad () {
        
    },

    start () {

    },
    showData : function () {
        var d = this._data;
        this.friendName.string = d.name
        this.shili.string = i18n.t("FRIEND_APPLY_SHILI", {num: d.shili});
        this.vip.string = i18n.t("FRIEND_APPLY_VIP", {num: d.vip});


        if(d.headavatar){
            this.userHead.setUserHead(d.job,d.headavatar);
        }
    },
    onClickDel : function () {
        let self = this;
        utils.utils.showConfirm(i18n.t("FRIEND_BLACK_REMOVE", {name: this._data.name}), function () {
            initializer.friendProxy.sendRemoveBlake(self._data.uid);
            // var p = new proto_cs.friends.subblacklist();
            // p.fuid = this._data.uid
            // JsonHttp.send(p,function(data){});  
        },this)
    },
    onClickFriendHead : function(){
        if (!!this._data) {
            initializer.friendProxy.sendGetOther(this._data.uid);
        }
        // var p = new proto_cs.user.getFuserMember();
        // p.id = this._data.uid
        // JsonHttp.send(p, function (data) {
        //     utils.utils.openPrefabView("friend/FriendInfo",false,data.a.user.fuser);
        // });
    }
});
