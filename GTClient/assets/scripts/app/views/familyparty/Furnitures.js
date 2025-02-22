var utils = require("Utils");
var List = require("List");
var Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var r = require("TimeProxy");
var a = require("Config");
var UIUtils = require("UIUtils");
let ItemSlotUI = require("ItemSlotUI")
cc.Class({
    extends: cc.Component,

    properties: {
        iamgebgs:[cc.Node],
        lists:List,

        nomNode:cc.Node,
        infoNode:cc.Node,
        fjNode:cc.Node,

        lbname:cc.Label,
        lbwx:cc.Label,
        lbctent:cc.Label,
        imagebig:UrlLoad,


        fjitems:[ItemSlotUI],

        noListImage:cc.Node,


        
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {

        this.modeType = 1  //1普通模式   2分解模式

        this.mtype = 1
        this.list = []

        this.fenjiedata = null

        this.infoNode.active = false

        facade.subscribe("FURNITURE_WAREHOUSE", this.showfurnitureList, this);
    },

    start () {
        this.reShowTopTypesBtn()
        this.checkMod(this.modeType)
        
        this.showfurnitureList()
    },

    showfurnitureList(){
        //localcache.getItem(localdb.table_furniture, 1)
        
        this.list.length = 0
        let furniture = Initializer.famUserHProxy.warehouse.haveFurniture
        let keys = Object.keys(furniture)
        let len = keys.length
        for (let i = 0; i < len; i++) {
            let id = keys[i]
            let data = localcache.getItem(localdb.table_furniture, id)
            if(data && furniture[id]>0){
                this.list.push(data)
            }
        }
        this.clearfjItems()
        this.showList()
    },

    showList(){
        //stytle
        let arrays = []
        for (let i = 0; i < this.list.length; i++) {
            let md = this.list[i];
            if(md.stytle === this.mtype){
                arrays.push(md)
            }
        }

        this.noListImage.active = arrays.length === 0
        this.lists.data = arrays
    },

    reShowTopTypesBtn(){
        let len = 4
        for (let i = 0; i < len; i++) {
            this.iamgebgs[i].active = (i+1) === this.mtype
        }
    },

    clearfjItems(){
        for (let i = 0; i < 3; i++) {
            this.fjitems[i].data = {
            }
        }
    },

    checkMod(type){
        //this.clearfjItems()
        this.infoNode.active = false
        this.modeType = type
        this.nomNode.active = this.modeType === 1
        this.fjNode.active = this.modeType === 2
        
    },

    onClickEnfj(t,e){
        this.checkMod(2)
        this.resHowFj()
    },

    onClickEnNom(){
        //this.fenjiedata = null
        this.checkMod(1)
    },

    onClickSureFj(){
        if(this.fenjiedata){

            let item = {
                kind:205,
                id:this.fenjiedata.data.id,
                count:1,
            }

            utils.utils.openPrefabView("familyparty/SelectFFnum", null, {item:item ,max: this.fenjiedata.count ,callBack:(num)=>{
                Initializer.famUserHProxy.sendMessagedecomposeFurniture(this.fenjiedata.data.id,num,
                ()=>{
                    if(num<this.fenjiedata.count){
                        this.fenjiedata.count-=num
                        this.resHowFj()
                    }else{
                        this.clearfjItems()
                        this.fenjiedata = null
                    }
                })
            }});

            
        }
    },


    onClickBtnOne(t,e){
        let type = parseInt(e)
        if(this.mtype != type){
            this.mtype = type
            this.reShowTopTypesBtn()
            this.showList()
        }
    },

    reShowNomNode(data){
        this.infoNode.active = true
        this.lbname.string = data.name
        this.lbwx.string = data.wenxin
        this.lbctent.string = data.desc
        this.imagebig.url = UIUtils.uiHelps.getFurnituresBigImage(data.picture)
    },

    resHowFj(){
        if(!this.fenjiedata){return}
        let decompose =  this.fenjiedata.data.decompose
        let count = this.fenjiedata.count

        for (let i = 0; i < 3; i++) {
            this.fjitems[i].data = {
                    id:decompose[i].id,
                    count:decompose[i].count*count,
                    kind:decompose[i].kind,
            }
        }
    },

    onClickItemOne(t,e){
        this.reShowNomNode(e._data)
        let furniture = Initializer.famUserHProxy.warehouse.haveFurniture
        let count = furniture[e._data.id]
        this.fenjiedata = {
                count:count,
                data:e._data,
        }
        if(this.modeType === 1){}else{
            this.resHowFj()
        }
    },
    
    
    onClickClose() {
        if(this.modeType === 2){
            this.onClickEnNom()
            return
        }
        utils.utils.closeView(this);
        facade.send("SHOWUSERHOME")
    },

    // update (dt) {},
});
