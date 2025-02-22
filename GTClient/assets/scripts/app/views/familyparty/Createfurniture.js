

var utils = require("Utils");
var List = require("List");
var Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var r = require("TimeProxy");
var a = require("Config");

let ItemSlotUI = require("ItemSlotUI")

cc.Class({
    extends: cc.Component,

    properties: {
        list:List,
        iamgebgs:[cc.Node],

        creating:cc.Node,
        willcreat:cc.Node,
        created:cc.Node,


        willitems:[ItemSlotUI],
        willNumbers:cc.Label,


        percentLabel:cc.Label,
        progress:cc.Node,
        pointn:cc.Node,

        touchNode:cc.Node,

        noListImage:cc.Node,

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

    onLoad () {
        this.mtype = 1
        this.warrays = []

        this.willCreatItems = []

        this.maxW = 477
        this.indexs = 0
        this.runCreating = false
        this.iscreatore = false

        this.haveDrawing = Initializer.famUserHProxy.warehouse.haveDrawing
        console.log("this.haveDrawing")
        console.log(this.haveDrawing)
    },

    start () {
        this.reShowTopTypesBtn()

        
        this.showWillItems()
        this.reshowList()

        this.touchNode.on(cc.Node.EventType.TOUCH_END, this.onTouchEnd, this, !0);
        this.touchNode._touchListener.setSwallowTouches(false);
    },

    onTouchEnd(){
        if(!this.iscreatore){
            return
        }
        this.created.active = false
        this.willcreat.active = true
    },

    reShowTopTypesBtn(){
        let len = 4
        for (let i = 0; i < len; i++) {
            this.iamgebgs[i].active = (i+1) === this.mtype
        }

    },

    reshowList(){
        this.warrays = []
        let listw = localcache.getList(localdb.table_furniture_drawing);
        let len = listw.length
        for (let i = 0; i < len; i++) {
            let mode = listw[i];
            if(mode.stytle === this.mtype && this.haveDrawing[mode.type] && this.haveDrawing[mode.type] === mode.id){
                this.warrays.push(mode)
            }
        }


        let arrays = []
        for (let i = 0; i < this.warrays.length; i++) {
            if(this.warrays[i].stytle === this.mtype){
                arrays.push(this.warrays[i])
            }
        }
        this.noListImage.active = arrays.length === 0
        this.list.data = arrays
    },

    showWillItems(){
        this.iscreatore = false
        let nn = 0
        for (let i = 0; i < 4; i++) {
            if(i<this.willCreatItems.length){
                let data = {
                    count:this.willCreatItems[i].num,
                    lock:0,
                    name:this.willCreatItems[i].mod.name,
                    id:this.willCreatItems[i].mod.id,
                    kind:205,
                    picture:this.willCreatItems[i].mod.picture,
                    quality:this.willCreatItems[i].mod.lv

                }
                this.willitems[i].data = data
                nn+=this.willCreatItems[i].ncion*this.willCreatItems[i].num
            }else{
                this.willitems[i].data = {lock:1}
            }
        }
        this.willNumbers.string = nn
    },

    onClickItems(t,e){
        let index = parseInt(e)
        if(index<this.willCreatItems.length){
            let modes = this.willCreatItems.splice(index,1)
            this.calculateMaterial(1,modes[0])
            this.showWillItems()
            this.reshowList()
        }
    },

    clearWillItems(){
        let len = this.willCreatItems.length
        for (let i = 0; i < len; i++) {
            this.calculateMaterial(1,this.willCreatItems[i])
        }
    },

    touchAddShow(){
        this.willcreat.active = true
        this.creating.active = false
        this.created.active = false
    },


    runCreatingFunc(dt){
        this.indexs+=dt
        let pre = this.indexs/2
        pre = pre>=1?1:pre
        let w = this.maxW*pre
        this.progress.width = w
        this.pointn.x = w
        this.percentLabel.string = parseInt(pre*100) + "%"
        if(pre === 1){
            this.runCreating = false
            this.willcreat.active = false
            this.creating.active = false
            this.created.active = true
            this.iscreatore = true
            Initializer.timeProxy.floatReward();
        }
    },

    showCreating(){
        this.iscreatore = false
        this.willcreat.active = false
        this.creating.active = true

        this.progress.width = 0
        this.pointn.x = 0
        this.percentLabel.string = 0 + "%"
        this.indexs = 0
        this.runCreating = true

    },

    onClickSureCreate() {
        let len = this.willCreatItems.length
        let mpas = {}
        for (let i = 0; i < len; i++) {
            this.calculateMaterial(1,this.willCreatItems[i])
            mpas[this.willCreatItems[i].mod.id] = this.willCreatItems[i].num
        }
        if(len>0){
            Initializer.famUserHProxy.sendMessageCreate(mpas,()=>{
                this.willCreatItems.length = 0
                this.showWillItems()
                this.showCreating()
            })
        }
    },

    onClickClose() {
        this.clearWillItems()
        utils.utils.closeView(this)
        facade.send("SHOWUSERHOME")
    },

    onClickBtnOne(t,e){
        let type = parseInt(e)
        if(this.mtype != type){
            this.mtype = type
            this.reShowTopTypesBtn()
            this.reshowList()
        }
    },

    calculateMaterial(v,mod){
        let haveMaterial = Initializer.famUserHProxy.warehouse.haveMaterial
        let w =  mod.mod
        let coms = w.compose
        for (let i = 0; i < coms.length; i++) {
            let compdata = coms[i]
            if(haveMaterial[compdata.id]){
                haveMaterial[compdata.id] += v*(mod.num*compdata.count)
            }
        }
    },


    hebingshuju(mod){
        let len = this.willCreatItems.length
        for (let i = 0; i < len; i++) {
            if(this.willCreatItems[i].mod.id === mod.mod.id){
                this.willCreatItems[i].num += mod.num
                return
            }
        }
        this.willCreatItems.push(mod)
    },

    
    onClickCreate(t,e){
        let max = e.amxNums
        if(this.willCreatItems.length>=4){

            //打造位置满了
            return
        }
        this.touchAddShow()
        let item = {
            kind:205,
            id:e._data.id,
            count:1,
        }
        utils.utils.openPrefabView("familyparty/SelectFFnum", null, {did:e._data, item:item, max: max ,callBack:(num)=>{
            if(num <= 0){
                return
            }
            let mod = {
                mod:e._data,
                num:num,
                ncion:e.ncion,
            }
            this.hebingshuju(mod)
            this.calculateMaterial(-1,mod)
            this.showWillItems()
            this.reshowList()
        }});
        
    },

    update (dt) {
        if(this.runCreating){
            this.runCreatingFunc(dt)
        }
    },
});
