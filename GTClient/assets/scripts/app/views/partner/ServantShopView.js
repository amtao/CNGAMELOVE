var i = require("List");
var Initializer = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        listView: i,
        seColor:cc.Color,
        norColor:cc.Color,
        lblTitles: [cc.Label],
        nBtnBgs: [cc.Node],
        btns: [cc.Button],
    },
    ctor() {
        this.allData = null;
        this.currentChoose = 0;
    },
    onLoad() {
        facade.subscribe("UPDATE_HEROSHOP", this.showData, this);
        let heroId = this.node.openParam.id;
        let type = this.node.openParam.type;
        //facade.subscribe("UPDATE_HERO_JB", this.updateJiban, this);
        this.allData = localcache.getFilters(localdb.table_heroshop,"belong_hero",heroId);
        this.onClickTab(null,type)
        //this.showData();
        
    },
    showData() {
        let listdata = this.allData.filter((data)=>{
            if (data.fenye == this.currentChoose){
                return true;
            }
            else
                return false;
        })
        let jibanlevel = Initializer.jibanProxy.getHeroJbLv(this.node.openParam.id).level % 1000;
        let shopdata = Initializer.servantProxy.heroShopData;
        let sortFunc = function(a){
            if (a.unlock_level != 0 && a.unlock_level <= jibanlevel){
                if (shopdata && shopdata.buy && shopdata.buy[a.id] != null &&  shopdata.buy[a.id] >= a.limit && a.limit != 0){
                    return 3;
                }
                else{
                    return 1;
                }
            }
            else{
                return 2;
            }
        };
        listdata.sort((a,b)=>{
            if (sortFunc(a) == sortFunc(b)){
                return a.id < b.id ? -1 : 1;
            }
            else{
                return sortFunc(a) < sortFunc(b) ? -1 : 1;
            }
        })
        this.listView.data = listdata;
    },
    closeBtn() {
        l.utils.closeView(this);
    },

    onClickTab(t, strIndex) {
        let index = parseInt(strIndex) - 1;
        if (index == 2 || index == 3){
            l.alertUtil.alert18n("PARYNER_ROOMTIPS35");
            return;
        }
        this.currentChoose = index + 1;
        for (let i = 0; i < this.btns.length; i++) {
            let bCur = index == i;
            this.btns[i].interactable = !bCur;
            this.nBtnBgs[i].active = bCur;
            this.lblTitles[i].node.color = bCur ? this.seColor: this.norColor;
        }
        this.showData();       
    },
});
