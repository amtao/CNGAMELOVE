var utils = require("Utils");
var roleSpine = require("RoleSpine");
var initializer = require("Initializer");
var chengHaoItem = require("ChengHaoItem");
var config = require("Config");
var timeProxy = require("TimeProxy");
cc.Class({
    extends: cc.Component,

    properties: {
        role:roleSpine,
        btnDel:cc.Node,
        btnBlack:cc.Node,
        btnAdd:cc.Node,
        btnJiuLuo: cc.Node,

        roleName:cc.Label,

        roleId:cc.Label,
        shenfen:cc.Label,
        gongdian:cc.Label,
        shili:cc.Label,
        wuNode: cc.Node,
        chenghao: chengHaoItem,
        chenghaoparentNode: cc.Node,

        wuli:cc.Label,
        zhengzhi:cc.Label,
        zhili:cc.Label,
        meili:cc.Label
    },

    onLoad () {
        facade.subscribe("JIU_LOU_INFO_BACK", this.onJiulouInfo, this);
        // facade.subscribe("FRIEND_BLACK_LIST", this.reflishBtn, this);
        this.role.setClothes(this.node["openParam"].sex,this.node["openParam"].job,this.node["openParam"].level,this.node["openParam"].clothe);

        // var roleNameStr = ""
        // for(var i=0;i<this.node["openParam"].name.length;i++){
        //     roleNameStr+=this.node["openParam"].name[i]
        //     if(i<this.node["openParam"].name.length-1){
        //         roleNameStr+="\n";
        //     }
        // }    

        this.roleName.string = this.node["openParam"].name;
        this.roleId.string = this.node["openParam"].id
        this.gongdian.string = this.node["openParam"].clubname || "--";
        var office = localcache.getItem(localdb.table_officer, this.node["openParam"].level);
        this.shenfen.string = office.name
        if (config.Config.isShowChengHao && timeProxy.funUtils.isOpenFun(timeProxy.funUtils.chenghao)) {
            this.chenghaoparentNode.active = !0;
            let t = this.node["openParam"].chenghao;
            if (t) {
                var e = localcache.getItem(localdb.table_fashion, t.chenghao);
                this.chenghao.data = e;
                this.wuNode.active = !e;
            } else {
                this.chenghao.data = null;
                this.wuNode.active = !0;
            }
        } else {
            this.chenghaoparentNode.active = !1;
        }
        this.shili.string = this.node["openParam"].shili

        this.meili.string = this.node["openParam"].ep.e4 + "";
        this.wuli.string = this.node["openParam"].ep.e1 + "";
        this.zhili.string = this.node["openParam"].ep.e2 + "";
        this.zhengzhi.string = this.node["openParam"].ep.e3 + "";

        this.reflishBtn();
    },

    start () {

    },
    reflishBtn(){
        this.btnJiuLuo.active = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.jiulouView);
        if(this.node["openParam"].id == initializer.playerProxy.userData.uid){
            this.btnDel.active = false
            this.btnAdd.active = false
            this.btnBlack.active = false
        }else{
            if(initializer.friendProxy.getFriendById(this.node["openParam"].id)){
                this.btnDel.active = true
            }else{
                this.btnDel.active = false
            }
    
            if(initializer.friendProxy.getFriendById(this.node["openParam"].id) || initializer.friendProxy.getFriendByBlack(this.node["openParam"].id))
                this.btnAdd.active = false
            else
                this.btnAdd.active = true
        }
    },
    onClickClose : function () {
        utils.utils.closeView(this, true,1);
    },
    onClickBlack : function () {
        var friendId = 0
        if(!this.node["openParam"].uid){
            friendId = this.node["openParam"].id
        }else{
            friendId = this.node["openParam"].uid
        }
        if(initializer.friendProxy.getFriendByBlack(friendId)){
            utils.alertUtil.alert(i18n.t("FRIEND_BLAKE_SUCCESS"));
            return 
        }
        utils.utils.showConfirm(i18n.t("FRIEND_BLAKE_ADD_TIP", {name: this.node["openParam"].name}), function () {
            var p = new proto_cs.friends.addblacklist();
            if(!this.node["openParam"].uid){
                p.fuid = this.node["openParam"].id
            }else{
                p.fuid = this.node["openParam"].uid
            }
            JsonHttp.send(p,function(){
                utils.alertUtil.alert(i18n.t("FRIEND_BLAKE_SUCCESS"));
            });  
        },this)
    },
    onClickDel : function () {
        utils.utils.showConfirm(i18n.t("FRIEND_DEL_TIP", {name: this.node["openParam"].name}), function () {
            let fuid =  this.node["openParam"].uid || this.node["openParam"].id
            initializer.friendProxy.sendDelFriend(fuid);
            // var p = new proto_cs.friends.fsub();
            // if(!this.node["openParam"].uid){
            //     p.fuid = this.node["openParam"].id
            // }else{
            //     p.fuid = this.node["openParam"].uid
            // }
            // JsonHttp.send(p,function(){});
        },this)
    },
    onAdd : function () {
        var p = new proto_cs.friends.fapply();
        if(!this.node["openParam"].uid){
            p.fuid = this.node["openParam"].id
        }else{
            p.fuid = this.node["openParam"].uid
        }
        JsonHttp.send(p,(data)=>{
            if (!(data.a && data.a.system && data.a.system.errror)) {
                utils.alertUtil.alert(i18n.t("FRIEND_APPLY_AGAIN"));
            }
        });
    },
    onClickYanhui : function () {
        // var yanhuiId = 0
        // if(!this.node["openParam"].uid){
        //     yanhuiId = this.node["openParam"].id
        // }else{
        //     yanhuiId = this.node["openParam"].uid
        // }
        // initializer.jiulouProxy.sendYhGo(yanhuiId);
        initializer.jiulouProxy.sendJlInfo();
    },
    
    onJiulouInfo() {
        if (null == initializer.jiulouProxy.getYhData(initializer.playerProxy.fuser.id)){
            utils.alertUtil.alert18n("JIU_LOU_MEI_YOU_JU_BAN");
        } else {
            var yanhuiId = this.node["openParam"].uid || this.node["openParam"].id;
            initializer.jiulouProxy.selectData = initializer.jiulouProxy.getYhData(yanhuiId);
            initializer.jiulouProxy.sendYhGo(yanhuiId);
        }
    },
});
