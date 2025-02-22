var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        listView: i,
        //lbljb: cc.Label,
        seColor:cc.Color,
        norColor:cc.Color,
        lblTitles: [cc.Label],
        nBtnBgs: [cc.Node],
        btns: [cc.Button],
    },
    ctor() {
        this.itemArr = [];
        this.belongArr = [];
        this.currentChoose = 0;
    },
    onLoad() {
        facade.subscribe("UPDATE_BAG_ITEM", this.showData, this);
        let heroId = this.node.openParam.id;
        //facade.subscribe("UPDATE_HERO_JB", this.updateJiban, this);
        for (var ii = 900; ii < 921;ii++){
            let cfg = localcache.getItem(localdb.table_item,ii);
            if (cfg.belong_hero != null && cfg.belong_hero.length > 0){
                if (cfg.belong_hero.indexOf(heroId) != -1){
                    this.belongArr.push({id:ii,heroid:heroId})
                }
            }
            else{
                this.itemArr.push({id:ii,heroid:heroId})
            }
            
        }
        this.onClickTab(null,1)
        //this.showData();
        
    },
    showData() {
        //this.listView.updateItemShow();
        if (this.currentChoose == 0){
            this.listView.data = this.itemArr;
        }
        else{
            this.listView.data = this.belongArr;
        }
    },
    closeBtn() {
        l.utils.closeView(this);
    },

    onClickTab(t, strIndex) {
        let index = parseInt(strIndex) - 1;
        if (index == 1){
            l.alertUtil.alert(i18n.t("COMMON_ZANWEIKAIQI"));
            return;
        }
        this.currentChoose = index;
        for (let i = 0; i < this.btns.length; i++) {
            let bCur = index == i;
            this.btns[i].interactable = !bCur;
            this.nBtnBgs[i].active = bCur;
            this.lblTitles[i].node.color = bCur ? this.seColor: this.norColor;
        }
        this.showData();       
    },
});
