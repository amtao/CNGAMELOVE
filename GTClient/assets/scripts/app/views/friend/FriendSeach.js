var utils = require("Utils");
var list = require("List");
var initializer = require("Initializer");
cc.Class({
    extends: cc.Component,

    properties: {
        roleName:cc.Label,
        myid:cc.Label,
        seachList :list,
        blankNode : cc.Node
    },

    onLoad () {
        //this.listAry = [];
        var userData = initializer.playerProxy.userData;
        this.myid.string = i18n.t("FRIEND_MY_ID", {num: userData.uid});
        facade.subscribe("UPDATE_RECOMMEND_LIST", this.onUpdateRecimmendList, this);
        facade.subscribe("FAPPLY_ID_LIST", this.onUpdateRecimmendList, this);
        facade.subscribe("ON_UPDATE_SEARCH_FRIEND", this.onUpdateFriendSearch, this);
        //this.onUpdateRecimmendList();
    },

    onUpdateFriendSearch(){
        this.seachList.data = initializer.friendProxy.seachList;
        this.blankNode.active = initializer.friendProxy.seachList.length <= 0;
    },

    onUpdateRecimmendList(){
        this.seachList.data = initializer.friendProxy.recommendList;
        this.blankNode.active = initializer.friendProxy.recommendList.length <= 0;
    },

    reflishSeach(isV){
        if(isV){
            this.node.active = true
        }else{
            this.node.active = false
        }
    },

    touchSeachBtn(){
        var userData = initializer.playerProxy.userData;
        // var self = this
        if(this.roleName.string == ""){
            utils.alertUtil.alert(i18n.t("FRIEND_SEARCH_NO_ID"));
        }else if(parseInt(userData.uid) == parseInt(this.roleName.string)){
            utils.alertUtil.alert(i18n.t("FRIEND_SEARCH_SELF"));
        }else{
            initializer.friendProxy.sendFriendSearch(this.roleName.string);
            // var p = new proto_cs.friends.search();
            // p.fuid = this.roleName.string
            // JsonHttp.send(p,function(data){
            //     if(data.a && data.a.search)
            //         self.seachList.data = [data.a.search.list]
            // });
        }
    },

    touchSeachChange(){
        initializer.friendProxy.sendFriendRecommend()
        // var self = this
        // var p = new proto_cs.friends.rlist();
        // JsonHttp.send(p,function(data){
        //     if(data.a){
        //         self.listAry = data.a.recommend.list
        //         self.seachList.data = data.a.recommend.list
        //     }
        // });
    }

});
