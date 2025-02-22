let List = require("List");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Utils = require("Utils");
var Initializer = require("Initializer");
var TimeProxy = require("TimeProxy");
let ShaderUtils = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: List,//可选列表
        lbltime:cc.Label,
    },
    ctor() {
        this.endTime = 0;
    },
    onLoad() {
        facade.subscribe("REFRESH_EXCHANGESHOPLIST", this.refreshList, this);
        this.refreshList();
        this.endTime = Utils.utils.getParamInt("fuyue_duihuan_time");
    },

    refreshList(){
        let listcfg = localcache.getList(localdb.table_duihuan);
        //先按是否已经达到限购上限然后再根据id排序
        listcfg.sort((a,b)=>{
            if (Initializer.fuyueProxy.getFYExchangeIsInLimit(a.id,a.set) == Initializer.fuyueProxy.getFYExchangeIsInLimit(b.id,b.set)){
                return a.id < b.id ? -1 : 1;
            }
            else{
                return Initializer.fuyueProxy.getFYExchangeIsInLimit(a.id,a.set) < Initializer.fuyueProxy.getFYExchangeIsInLimit(b.id,b.set) ? -1 : 1;
            }
        })
        this.list.data = listcfg;
    },

   onClickClose(){
        Utils.utils.closeView(this);
   },

   update(dt){
        let remaintime = this.endTime - Utils.timeUtil.second;
        if (remaintime <= 0){
            this.lbltime.string = "";
            return;
        }
        this.lbltime.string = Utils.timeUtil.second2hms(remaintime);
   },
    

});
