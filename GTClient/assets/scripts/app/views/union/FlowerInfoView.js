
var l = require("Utils");
var urlLoad = require("UrlLoad");
var List = require("List");
let initializer = require("Initializer");
var RwdItem = require("RwdItem");
cc.Class({
    extends: cc.Component,

    properties: {

        lbName:cc.Label,
        head:urlLoad,
        tcImg:cc.Node,
        lbGetNum:cc.Label,
        topRwd:RwdItem,
        jiliList:[cc.Label],
        jiliNumberList:[cc.Label],
        jiliImageList:[cc.Node],
        getList:[List],
        mygetjili:cc.Label,
        lbMyGetNum:cc.Label,
        bottom:cc.Node,
        myRwd:RwdItem,

        // foo: {
        //     // ATTRIBUTES:
        //     default: null,        // The default value will be used only when the component attaching
        //                           // to a node for the first time
        //     type: cc.SpriteFrame, // optional, default is typeof default
        //     serializable: true,   // optional, default is true
        // },
        // bar: {
        //     get () {
        //         return this._bar;
        //     },
        //     set (value) {
        //         this._bar = value;
        //     }
        // },
    },

    // LIFE-CYCLE CALLBACKS:

    // onLoad () {},

    start () {
        // initializer.playerProxy.loadUserHeadPrefab(this.head,initializer.playerProxy.headavatar); 
        // this.lbName.string = initializer.playerProxy.userData.name;
        let awardmap = initializer.unionProxy.throwPotData.awardInfo
        this.fstData = null
        this.puuid = 0
        this.mydata = null
        this.myindex = -1
        for (let i = 0; i < 3; i++) {
            let awadata = awardmap[i+1]
            if(awadata){
                let keys = Object.keys(awadata)
                let length = keys.length;
                this.jiliNumberList[i].string = length
                let array = []
                for (let j = 0; j < length; j++) {
                    let key = keys[j]
                    let infodata = awadata[key]
                    if(this.fstData === null){
                        this.fstData = infodata
                        this.puuid = key 
                        let fsd = this.puuid
                    }else{
                        this.fstData = infodata.count>this.fstData.count?(this.puuid = key,infodata):this.fstData
                    }
                    array.push(infodata)
                    if(initializer.playerProxy.userData.uid == key){
                        this.jiliImageList[i].active = true
                        this.myindex = i
                        this.mydata = infodata
                    }
                }
                this.getList[i].data = array
            }
        }
        this.showTopAndbottom()
    },

    showTopAndbottom(){
        this.showTopFst()
        this.showMine()
    },
    showMine(){
        if(this.myindex === -1){
            this.bottom.active = false
        }else{
            this.mygetjili.string =  i18n.t("FLOWER_GET_MID_ITEM"+(this.myindex+1))
            this.myRwd.data = this.mydata
        }
    },

    showTopFst(){
        if(this.puuid != 0){
            //uid 取头像
            initializer.playerProxy.getUserBaseInfo(this.puuid,(data)=>{
                initializer.playerProxy.loadUserHeadPrefab(this.head,data.a.user.baseInfo.info.headavatar)
                this.lbName.string = data.a.user.baseInfo.info.name
            })
        }
        if(this.fstData){
            this.topRwd.data = this.fstData
        }
    },

    eventClose() {
        l.utils.closeView(this);
    },

    // update (dt) {},
});
