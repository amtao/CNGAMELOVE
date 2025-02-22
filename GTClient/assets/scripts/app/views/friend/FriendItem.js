var utils = require("Utils");
var renderListItem = require("RenderListItem");
var userHeadItem = require("UserHeadItem");
var initializer = require("Initializer");
cc.Class({
    extends: renderListItem,

    properties: {
        friendName:cc.Label,
        friendPower:cc.Label,
        friendVip:cc.Label,
        haoganLab: cc.Label,
        userHead : userHeadItem,
        isCheck : cc.Node,
        loginTime : cc.Label,
        sendGiftBtn: cc.Button,
        haoGanIcoSpr: cc.Sprite,
        haoGanIcoFrames: [cc.SpriteFrame],
    },

    start () {
        facade.subscribe("CHOOSE_FRIEND", this.reflishTouch, this);
    },    
    showData : function () {
        var d = this._data;
        this.uid = d.uid;

        this.friendName.string = d.name;
        this.haoganLab.string = d.love + "";
        this.friendPower.string = i18n.t("FRIEND_APPLY_SHILI", {num: d.shili + ""});
        this.friendVip.string = i18n.t("FRIEND_APPLY_VIP", {num: d.vip + ""});
        let haoGanLv = initializer.friendProxy.getHaoGanLv(parseInt(d.love));
        haoGanLv = Math.min(this.haoGanIcoFrames.length, haoGanLv);//避免好感配置等级超了3级，对应icon不够
        this.haoGanIcoSpr.spriteFrame = this.haoGanIcoFrames[haoGanLv - 1];
        this.userHead.setUserHead(d.job,d.headavatar);

        this.loginTime.string = utils.timeUtil.getDateDiff(d.lastlogin);
        this.sendGiftBtn.interactable = !d.isSend;
        this.reflishTouch();
    },
    reflishTouch(){
        this.isCheck.active = this.uid != initializer.friendProxy.viewChooseId
    },
    touchBtn(){
        initializer.friendProxy.viewChooseId = this.uid
        facade.send("CHOOSE_FRIEND");
    },
    onClickSendGift(){
        initializer.friendProxy.sendGift(this.uid);
    },
    onClickFriendHead : function(){
        initializer.friendProxy.sendGetOther(this.uid);
        // var friend = initializer.friendProxy.getFriendById(this.uid);
        // if(friend){
        //     var p = new proto_cs.user.getFuserMember();
        //     p.id = this.uid;
        //     JsonHttp.send(p, function (data) {
        //         //console.log(data)
        //         utils.utils.openPrefabView("friend/FriendInfo",false,data.a.user.fuser);
        //     });
        // }
    }
});
