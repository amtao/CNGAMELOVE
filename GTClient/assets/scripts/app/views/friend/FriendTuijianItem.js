var renderListItem = require("RenderListItem");
var initializer = require("Initializer");
var utils = require("Utils");
cc.Class({
    extends: renderListItem,

    properties: {
        friendName : cc.Label,
        friendPower : cc.Label,
        friendVip : cc.Label,
        shenqingLabel :cc.Label,

        shenqingBtn : cc.Button
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
        this.setApplyBtnStr();
    },
    touchShenqing :function (){
        if(initializer.friendProxy.getFriendByBlack(this.uid)){
            utils.alertUtil.alert(i18n.t("FRIEND_BLAKE_SUCCESS"));
            return 
        }
        if(initializer.friendProxy.getFriendById(this.uid)){
            utils.alertUtil.alert(i18n.t("FRIEND_MEET_OLD"));
            return 
        }
        initializer.friendProxy.sendFriendApply(this.uid);
        // var p = new proto_cs.friends.fapply();
        // p.fuid = this.uid;
        // JsonHttp.send(p,function (data) {
        //     //utils.alertUtil.alert("已经申请过了，请小主耐心等待");
        // });
    },
    onClickFriendHead : function(){
        initializer.friendProxy.sendGetOther(this.uid);
        // // var friend = initializer.friendProxy.getFriendById(this.uid);
        // // if(friend){
        //     var p = new proto_cs.user.getFuserMember();
        //     p.id = this.uid;
        //     JsonHttp.send(p, function (data) {
        //         utils.utils.openPrefabView("friend/FriendInfo",false,data.a.user.fuser);
        //     });
        // // }
    },
    setApplyBtnStr : function (){
        if((this._data && !!this._data.isApply) || initializer.friendProxy.isInApply(this.uid)){//initializer.friendProxy.isInApply(this.uid)
            this.shenqingLabel.string = i18n.t("FRIEND_APPLY_OVER")
            this.shenqingBtn.interactable = false;
        }else{
            this.shenqingLabel.string = i18n.t("FRIEND_APPLY")
            this.shenqingBtn.interactable = !initializer.friendProxy.getFriendById(this.uid);
        }
    }
});
