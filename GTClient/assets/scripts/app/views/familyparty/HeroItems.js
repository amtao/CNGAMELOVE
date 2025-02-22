

let item = require("RenderListItem");

var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var utils = require("Utils");

cc.Class({
    extends: item,

    properties: {
        hero:UrlLoad,
        lbname:cc.Label,
        txt:cc.Node,
        iselect:cc.Node,
        btnSelect:cc.Button,
        tjNode:cc.Node,
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
        this.btnSelect && this.btnSelect.clickEvents && this.btnSelect.clickEvents.length > 0 && (this.btnSelect.clickEvents[0].customEventData = this);
    },

    start () {

    },

    showData() {
        let data = this._data;
        if(data) {
            let hookinfo = {}
            this.lbname.string = localcache.getItem(localdb.table_hero, data.id).name
            this.hero.url = UIUtils.uiHelps.getServantHead(data.id);
            this.iselect.active = Initializer.famUserHProxy.selectHid === data.id
            if(Initializer.famUserHProxy.selectMode === 2){
                this.txt.active = false
            }else{
                if(!Array.isArray(Initializer.famUserHProxy.hook.hookInfo) && Initializer.famUserHProxy.hook.hookInfo){
                    hookinfo = Initializer.famUserHProxy.hook.hookInfo
                }
                let keys = Object.keys(hookinfo)
                this.txt.active = false
                this.iscan = true
                for (let i = 0; i < keys.length; i++) {
                    let nm = hookinfo[keys[i]]
                    if(nm.heroId === data.id && utils.timeUtil.second - nm.hookEndTime<0){
                        this.txt.active = true
                        this.iscan = false
                        break;
                    }
                }
            }
            this.tjNode.active = false
            let indday = new Date(utils.timeUtil.second*1000).getDay()-1
            indday = indday<0?0:indday
            if(this.iscan && Initializer.famUserHProxy.selectCT.heroneed && data.id === Initializer.famUserHProxy.selectCT.heroneed[indday]){
                this.tjNode.active = true
            }
            if(Initializer.famUserHProxy.selectMode === 2){
                this.txt.active = false
                this.tjNode.active = false
            }
            
        }
    },

    // update (dt) {},
});
