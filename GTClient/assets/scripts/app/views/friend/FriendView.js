var utils = require("Utils");
var urlLoad = require("UrlLoad");
var list = require("List");
var initializer = require("Initializer");
var timeProxy = require("TimeProxy");
cc.Class({
    extends: cc.Component,

    properties: {
        role:urlLoad,
        friendList :list,
        friendNum : cc.Label,

        kuoRongBtn : cc.Node
    },
    onLoad(){
        var p = new proto_cs.friends.flist();
        JsonHttp.send(p,function(){});
        facade.subscribe("UPDATE_FRIEND_LIST", this.reflishList, this);
        facade.subscribe(initializer.playerProxy.PLAYER_USER_UPDATE, this.reflishFriendNum, this);
    },
    start () {
        initializer.playerProxy.loadPlayerSpinePrefab(this.role);
    },
    onDestroy(){
        initializer.friendProxy.viewChooseId = 0;
    },
    onClickClost : function () {
        utils.utils.closeView(this, true,1);
    },
    onClickBlack : function(){
        var p = new proto_cs.friends.blacklist();
        JsonHttp.send(p,function(data){
            utils.utils.openPrefabView("friend/FriendBlack");
        });
    },
    onClickAdd : function(){
        initializer.friendProxy.sendFriendRecommend(function(data){
            if(data.a){
                utils.utils.openPrefabView("friend/FriendAdd",null);
            }else{
                utils.utils.openPrefabView("friend/FriendAdd",null);
            }
        })
        // var p = new proto_cs.friends.rlist();
        // JsonHttp.send(p,function(data){
        //     if(data.a){
        //         utils.utils.openPrefabView("friend/FriendAdd",null,data.a.recommend.list);
        //     }else{
        //         utils.utils.openPrefabView("friend/FriendAdd",null,[]);
        //     }
        // });
    },
    onClickDel : function(){
        if(initializer.friendProxy.viewChooseId > 0){
            var friend = initializer.friendProxy.getFriendById(initializer.friendProxy.viewChooseId);
            if(friend){
                utils.utils.showConfirm(i18n.t("FRIEND_DEL_TIP", {name: friend.name}), function () {
                    // var p = new proto_cs.friends.fsub();
                    // p.fuid = initializer.friendProxy.viewChooseId
                    // JsonHttp.send(p,function(){});
                    initializer.friendProxy.sendDelFriend(initializer.friendProxy.viewChooseId)
                },this)
            }
        }
    },
    onClickKuorong : function(){
        utils.alertUtil.alert(i18n.t("FRIEND_KUO_RONG_TIP"));
        timeProxy.funUtils.openView(timeProxy.funUtils.recharge.id,{type:1});
    },
    onClickChat : function(){
        if (!timeProxy.funUtils.isOpenFun(timeProxy.funUtils.chatView)) {
            timeProxy.funUtils.openView(timeProxy.funUtils.chatView.id);
            return;
        }
        if(initializer.friendProxy.viewChooseId > 0){
            initializer.friendProxy.sendGetFriendChat(initializer.friendProxy.viewChooseId, function(data){
                utils.utils.openPrefabView("chat/ChatView", false, { type: 6 });
            })
            // var p = new proto_cs.friends.ffchat();
            // p.fuid = initializer.friendProxy.viewChooseId
            // JsonHttp.send(p,function(data){
            //     utils.utils.openPrefabView("chat/ChatView", false, { type: 6 });
            // });
        }else{
            utils.utils.openPrefabView("chat/ChatView", false, { type: 6 });
        }
    },
    reflishList:function(){
        if(initializer.friendProxy.friendList.length > 0){
            initializer.friendProxy.viewChooseId = initializer.friendProxy.friendList[0].uid;
        }else{
            initializer.friendProxy.viewChooseId = 0;
        }
        this.friendList.data = initializer.friendProxy.friendList;
        this.reflishFriendNum();
    },
    reflishFriendNum : function (){
        let maxVipLv = initializer.playerProxy.getMaxVipLv();
        var vipJson = localcache.getItem(localdb.table_vip, initializer.playerProxy.userData.vip);
        let maxVipJson = localcache.getItem(localdb.table_vip, maxVipLv);
        this.friendNum.string = i18n.t("FRIEND_NUM", {num1:initializer.friendProxy.friendList.length, num2:vipJson["friendNum"]});
        this.kuoRongBtn.active = vipJson["friendNum"] < maxVipJson["friendNum"];
    }
});
