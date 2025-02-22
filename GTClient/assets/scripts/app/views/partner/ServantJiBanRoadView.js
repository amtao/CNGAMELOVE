var i = require("List");
var Initializer = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        listView: i,
        //lbljb: cc.Label,
    },
    ctor() {
        this.listdata2 = [];
    },
    onLoad() {
        let heroId = this.node.openParam.id;
        facade.subscribe("UPDATE_JIBANAWARD", this.updateData, this);
        //this.showData();
        let listdata  = localcache.getFilters(localdb.table_hero_yoke_unlock,"hero_id",heroId);
        let listdata2 = listdata.filter((data)=>{
            if (data.type != 4 && data.type != 0){
                return true;
            }
            return false;
        });
        this.listdata2 = listdata2;
        this.updateData();
    },

    updateData(){
        let heroId = this.node.openParam.id;
        let jibanlevel = Initializer.jibanProxy.getHeroJbLv(heroId).level;
        let sortFunc = function(a){
            if (a.yoke_level <= jibanlevel){
                if (a.type == 3){
                    if (Initializer.servantProxy.servanetJiBanAward != null && Initializer.servantProxy.servanetJiBanAward.pickInfo != null && Initializer.servantProxy.servanetJiBanAward.pickInfo.indexOf(a.id) != -1){
                        return 3
                    }
                    else{
                        return 1
                    }       
                }
                else{
                    return 3;
                }
            }
            else{
                return 2;
            }
        }
        this.listdata2.sort((a,b)=>{
            if (sortFunc(a) == sortFunc(b)){
                return a.id < b.id ? -1 : 1;
            }
            else{
                return sortFunc(a) < sortFunc(b) ? -1 : 1;
            }
        })

        this.listView.data = this.listdata2;
    },

    closeBtn() {
        l.utils.closeView(this);
    },

    
});
