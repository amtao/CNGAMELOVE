var utils = require("Utils");
var initializer = require("Initializer");
var redDot = require("RedDot");

const INIT_NO = 0;
const INIT_FRIEND_DATA = 1 << 1;
const INIT_CHAT_DATA = 1 << 2;
const INIT_OVER = INIT_CHAT_DATA | INIT_FRIEND_DATA;

function FriendProxy() {
    this.friendList = [];
    this.fapplyList = [];
    this.blackList= [];
    this.applyIdList = [];
    this.chatObj = {}
    this.chatLastObj = {}
    this.friendNumber = 0;
    this.viewChooseId = 0;

    this.ctor = function () {
        JsonHttp.subscribe(proto_sc.friends.flist, this.onUpdateFriend, this);
        JsonHttp.subscribe(proto_sc.friends.fapplylist, this.onUpdateApply, this);
        // JsonHttp.subscribe(proto_sc.chat.blacklist, this.onBlackList, this);
        JsonHttp.subscribe(proto_sc.friends.fllist,this.onUpdateChat,this);
        // JsonHttp.subscribe(proto_sc.friends.redPoint,this.reflishFriendApply,this);
        JsonHttp.subscribe(proto_sc.friends.applyList,this.onApplyIdList,this);
        // JsonHttp.subscribe(proto_sc.friends.flove, this.onFriendlove, this);
        JsonHttp.subscribe(proto_sc.recommend.list, this.onUpdateRecommendList, this);
        JsonHttp.subscribe(proto_sc.search.list, this.onSearchList, this);
        this.clearData();
    };

    this.clearData = function(){
        this.friendLastMsgIdMap = null;
        this.chatObj = {};
        this.chatLastObj = {};
        this.friendList = [];
        this.fapplyList = [];
        this.blackList= [];
        this.applyIdList = [];
        this.recommendList = [];
        this.seachList = [];
        this.friendNumber = 0;
        this.viewChooseId = 0;
        this.curChatFriendId = 0;
        this.initFlag = INIT_NO;
    }

//#region 与服务器交互
    //--------------------------------------------------- SC Begin --------------------------------------------------- 
    
    // this.onBlackList = function(p){
    //     this.blackList = p
    //     facade.send("FRIEND_BLACK_LIST");
    // }

    // this.reflishFriendApply = function(p){
    //     redDot.change("FRIEND_APPLY", p.status == 1);
    // }

    /**
     * 收到 好友查询
     */
    this.onSearchList = function(vo){
        this.seachList = !!vo ? [vo] : [];
        facade.send("ON_UPDATE_SEARCH_FRIEND");
    };

    /**
     * 更新 好友推荐 列表
     */
    this.onUpdateRecommendList = function(vo){
        this.recommendList = vo || [];
        facade.send("UPDATE_RECOMMEND_LIST");
    }

    /**
     * 收到 主动好友申请
     */
    this.onApplyIdList = function(p){
        this.applyIdList = p || [];
        facade.send("FAPPLY_ID_LIST");
    };

    /**
     * 更新 好友 数据
     */
    this.onUpdateFriend = function(p){
        this.friendList = p || [];
        this.initFlag |= INIT_FRIEND_DATA;
        this.handleChatMsg();
        facade.send("UPDATE_FRIEND_LIST");
    };

    /**
     * 收到 被邀请数据
     */
    this.onUpdateApply = function(p){
        this.fapplyList = p || [];
        redDot.change("FRIEND_APPLY", this.fapplyList.length >= 1);
        facade.send("FAPPLY_FRIEND");
    };

    /**
     * 收到 好友私聊
     */
    this.onUpdateChat = function(p){
        let chatList = p || [];
        this.chatList = {};
        for (let i = 0; i < chatList.length; i++) {
            const chat = chatList[i];
            !!chat.id && (this.chatList[chat.id] = chat);
        }
        this.initFlag |= INIT_CHAT_DATA;
        this.handleChatMsg();
    };

    // this.onFriendlove = function(vo){
    //     let friend = this.getFriendById(vo.send[vo.send.length - 1]);
    //     if (!!friend) {
    //         utils.alertUtil.alert(i18n.t("FRIEND_SEND_GIFT_TIP", {name: friend.name}));
    //     }
    // };
    
    //--------------------------------------------------- SC End --------------------------------------------------- 

    //--------------------------------------------------- CS Begin --------------------------------------------------- 
    /**
     * 获取 好友查询数据
     */
    this.sendFriendSearch = function(fuid){
        var p = new proto_cs.friends.search();
        p.fuid = fuid;
        JsonHttp.send(p);
    }

    /**
     * 获取 好友申请数据
     */
    this.sendGetFriendApply = function(){
        var p = new proto_cs.friends.fapplylist();
        JsonHttp.send(p);
    }
    /**
     * 获取其他玩家数据
     */
    this.sendGetOther = function(uid, spid = 0) {
        var p = new proto_cs.user.getFuserMember();
        p.id = uid;
        0 != spid && (p.spid = spid);
        JsonHttp.send(p, function (data) {
            if (!!data && !!data.a && !!data.a.user && !!data.a.user.fuser) {
                utils.utils.openPrefabView("friend/FriendInfo",false,data.a.user.fuser);
            }
        });
    };

    /**
     * 送礼
     * @param {*} fuid 好友uid
     */
    this.sendGift = function(fuid){
        var p = new proto_cs.friends.sendGift();
        p.fuid = fuid;
        let self = this;
        JsonHttp.send(p, function(data){
            if (!(data.a && data.a.system && data.a.system.errror)) {
                let friend = self.getFriendById(fuid);
                if (!!friend) {
                    utils.alertUtil.alert(i18n.t("FRIEND_SEND_GIFT_TIP", {name: friend.name, num: 1}));
                }
            }
        });
    }

    /**
     * 删除好友
     * @param {*} fuid 好友uid
     */
    this.sendDelFriend = function(fuid){
        var p = new proto_cs.friends.fsub();
        p.fuid = fuid
        JsonHttp.send(p,function(data){
            if (!(data.a && data.a.system && data.a.system.errror)) {
                utils.alertUtil.alert18n("FRIEND_DEL_SUCCESS");
            }
        });
    }

    /**
     * 移出黑名单
     * @param {*} fuid 好友uid
     */
    this.sendRemoveBlake = function(fuid){
        var p = new proto_cs.friends.subblacklist();
        p.fuid = fuid;
        JsonHttp.send(p,function(data){
            if (!(data.a && data.a.system && data.a.system.errror)) {
                utils.alertUtil.alert18n("FRIEND_BLACK_REMOVE_SUCCESS");
            }
        });  
    };

    /**
     * 获取 聊天数据
     * @param {*} fuid 好友uid
     */
    this.sendGetFriendChat = function(fuid, callback){
        if (!this.isTalkWithFriend()) {
            this.curChatFriendId = fuid;
            var p = new proto_cs.friends.ffchat();
            p.fuid = fuid;
            let self = this;
            this.isGetFriendChatData = true;
            JsonHttp.send(p, function(data){
                self.isGetFriendChatData = false;
                self.enterChatWithFriend(fuid);
                callback && callback(data);
            });
        }
    }

    /**
     * 发送 好友聊天 消息
     *  @param {*} msg 聊天消息
     */
    this.sendFriendChat = function(msg, callback){
        if (this.isTalkWithFriend()) {
            var p = new proto_cs.friends.fschat();
            p.fuid = this.curChatFriendId;
            p.msg = msg;
            JsonHttp.send(p,callback);
        }
    }

    /**
     * 发送 好友申请
     * @param {*} uid 申请好友id
     */
    this.sendFriendApply = function(uid){
        var p = new proto_cs.friends.fapply();
        p.fuid = uid;
        JsonHttp.send(p,function (data) {
            if (!(data.a && data.a.system && data.a.system.errror)) {
                utils.alertUtil.alert18n("FRIEND_APPLY_SUCCESS");
            }
        });
    }

    /**
     * 发送好友推荐
     */
    this.sendFriendRecommend = function(callback){
        var p = new proto_cs.friends.rlist();
        JsonHttp.send(p,callback);
    }

    /**
     * 发送 刷新私聊数据
     * @param {*} fuid 好友id
     */
    this.sendRefreshFriendChat = function(fuid){
        var p = new proto_cs.friends.frchat();
        p.fuid = fuid;
        JsonHttp.send(p);
    }

    /**
     * 刷新当前 私聊数据
     */
    this.sendRefreshCurFriendChat = function(){
        if (this.curChatFriendId > 0) {
            this.sendRefreshFriendChat(this.curChatFriendId);
        }
    }

    //--------------------------------------------------- CS End --------------------------------------------------- 

//#endregion
   
    this.isInApply = function(id){
        for(var i=0;i<this.applyIdList.length;i++){
            if(this.applyIdList[i] == id){
                return true
            }
        }
        return false;
    },

    this.handleChatMsg = function(){
        if (this.initFlag >= INIT_OVER) {

            //读取本地数据 初始化 消息 红点 数据
            if (this.friendLastMsgIdMap == null) {
                let str = initializer.timeProxy.getLoacalValue("FRIEND_LAST_MSG_ID");
                this.friendLastMsgIdMap = !utils.stringUtil.isBlank(str) ? JSON.parse(str) : {};
            }

            //更新 本地 消息 红点 缓存
            for (const uid in this.friendLastMsgIdMap) {
                if (this.friendLastMsgIdMap.hasOwnProperty(uid)) {
                    let flag = false;
                    for (let j = 0; j < this.friendList.length; j++) {
                        const friend = this.friendList[j];
                        if (friend.uid == uid) {
                            flag = true;
                            break;
                        }
                    }
                    if (!flag) {
                        delete this.friendLastMsgIdMap[uid];
                    }
                }
            }
            this.saveMsgInfo();
            let newMsgFlag = false;
            for (let i = 0; i < this.friendList.length; i++) {
                const friend = this.friendList[i];
                let chat = this.chatList[friend.uid];
                if (!!chat) {
                    let fuid = friend.uid;
                    let sllist = chat["sllist"];
                    if(!this.chatObj[fuid]){
                        this.chatObj[fuid] = [];
                    }
                    
                    for(var z=0;z<sllist.length;z++){
                        if(!!sllist[z].msg && this.isNewMsg(this.chatObj[fuid],sllist[z].id)){
                            this.chatObj[fuid].push(sllist[z]);
                            newMsgFlag |= true;
                        }
                    }
                    this.chatObj[fuid].sort(function(a, b){
                        return a.id - b.id;
                    })
                    if (sllist.length > 0) {
                        let lastMsg = sllist[sllist.length - 1];
                        // //1、自己发的消息 不需要新消息提示 2、当前正在跟玩家私聊 不需要新消息提示 判定有前后依赖关系
                        // if (!(lastMsg.uid == initializer.playerProxy.userData.uid || this.isEnterFriendTalk)) {
                        //     if(!this.friendLastMsgIdMap[lastMsg.uid] || lastMsg.id > this.friendLastMsgIdMap[lastMsg.uid].id){
                        //         this.friendLastMsgIdMap[lastMsg.uid] = {id: lastMsg.id, new: 1};
                        //         this.saveMsgInfo();
                        //     }
                        // }
                        if (this.isTalkWithFriend() && this.curChatFriendId == fuid) {
                            this.friendLastMsgIdMap[fuid] = {id: lastMsg.id};
                            this.saveMsgInfo();
                        }
                        //保存最新聊天数据
                        this.chatLastObj[fuid] = {}
                        this.chatLastObj[fuid] = sllist[sllist.length - 1];
                    }
                }
            }
            //1、新数据 要刷新 列表 2、初始化数据 要刷新列表
            if (newMsgFlag || this.isGetFriendChatData) {
                this.changeMsgRed();
                facade.send("FRIEND_CHAT_LIST");
            }
        }
    }

    /**
     * 判定是否是新消息
     */
    this.isNewMsg = function(list,id){
        for(var i=0;i<list.length;i++){
            if(list[i].id == id){
                return false
            }
        }
        return true
    }

    /**
     * 获取好友数据
     * @param {*} fuid 好友uid
     */
    this.getFriendById = function(fuid){
        for(var i=0;i<this.friendList.length;i++){
            if(this.friendList[i].uid == fuid)
                return this.friendList[i]
        }
    }

    /**
     * 获取黑名单数据
     * @param {*} uid 玩家uid
     */
    this.getFriendByBlack = function(uid){
        for(var i=0;i<this.blackList.length;i++){
            var id = this.blackList[i].uid?this.blackList[i].uid:this.blackList[i].id
            if(id == uid)
                return this.blackList[i]
        }
    }

    /**
     * 获取好感等级
     * @param {*} haoGan 与好友的好感值
     */
    this.getHaoGanLv = function(haoGan){
        let haoGanStrs = utils.utils.getParamStrs("friend_haogan_exp")
        for (let i = 0; i < haoGanStrs.length; i++) {
            if (haoGan < parseInt(haoGanStrs[i][0])) {
                return i + 1;
            }
        }
        return haoGanStrs.length + 1;
    }

    /**
     * 更新 消息红点状态
     */
    this.changeMsgRed = function(){
        let newMsgRed = false;
        // for (const uid in this.friendLastMsgIdMap) {
        //     if (this.friendLastMsgIdMap.hasOwnProperty(uid)) {
        //         const msg = this.friendLastMsgIdMap[uid];
        //         newMsgRed |= msg.new;
        //     }
        // }
        //JSHS 2020/5/11 改成 是好友 并且 有聊天数据 才判断红点
        for(var i=0;i<this.friendList.length;i++){
            let fuid = this.friendList[i].uid;
            let chatList = this.chatObj[fuid];
            let msg = this.friendLastMsgIdMap[fuid];
            if (chatList && chatList.length > 0) {
                newMsgRed |= !msg || msg.id < chatList[chatList.length - 1].id;
            }
        }
        redDot.change("friendNewMsg", newMsgRed);
    }

    /**
     * 获取有聊天记录的好友
     */
    this.getChatFriendList = function(){
        let tempList = []
        for (let i = 0; i < this.friendList.length; i++) {
            const friend = this.friendList[i];
            if(this.chatObj.hasOwnProperty(friend.uid) && this.chatObj[friend.uid].length > 0){
                let newData = {};
                utils.utils.copyData(newData, friend)
                tempList.push(newData);
            }
        }
        return tempList;
    }

    /**
     * 获取好友聊天数据
     * @param {*} fuid 好友uid
     */
    this.getFriendChatList = function(fuid){
        return !!fuid ? this.chatObj[fuid] || [] : [];
    }

    /**
     * 获取当前正在聊天的好友聊天数据
     */
    this.getCurFriendChatList = function(){
        return this.getFriendChatList(this.curChatFriendId);
    }

    /**
     * 与 好友聊天
     * @param {*} fuid 好友uid
     */
    this.enterChatWithFriend = function(fuid){
        let chatList = this.chatObj[fuid];
        if (!!chatList && chatList.length > 0) {
            !this.friendLastMsgIdMap[fuid] && (this.friendLastMsgIdMap[fuid] = {});
            this.friendLastMsgIdMap[fuid].id = chatList[chatList.length - 1].id;
        }
        this.changeMsgRed();
        this.saveMsgInfo();
    }

    /**
     * 离开好友私聊界面
     */
    this.leaveChatWithFriend = function(){
        this.curChatFriendId = 0;
    }
    /**
     * 判断是否处于与玩家聊天状态
     */
    this.isTalkWithFriend = function(){
        return this.curChatFriendId > 0;
    }

    /**
     * 获取 正在私聊好友 的数据
     */
    this.getChatFriendData = function(){
        return this.isTalkWithFriend() ? this.getFriendById(this.curChatFriendId) : null;
    }

    /**
     * 判定 与 fuid 好友 聊天 红点
     * @param {*} fuid 好友uid
     */
    this.checkTalkRed = function(fuid){
        let friend = this.getFriendById(fuid);
        let red = true;
        if (!!friend) {
            let fuid = friend.uid;
            let chatList = this.chatObj[fuid];
            let msg = this.friendLastMsgIdMap[fuid];
            red = !!chatList && chatList.length > 0 && (!msg || msg.id < chatList[chatList.length - 1].id);
        }else{
            red = false;
        }
        return red;
    }

    /**
     * 本地持久化 聊天消息 红点转态
     */
    this.saveMsgInfo = function(){
        initializer.timeProxy.saveLocalValue("FRIEND_LAST_MSG_ID", JSON.stringify(this.friendLastMsgIdMap));
    }
}


exports.FriendProxy = FriendProxy;