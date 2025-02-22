let List = require("List");
let Utils = require("Utils");
let Initializer = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list:List,
        lblCost:cc.Label,
    },
    onLoad() {
        this.target = this.node.openParam.Target;
        let allChooseCfg = this.node.openParam.AllChooseCfg;
        this.allChooseCfg = {};
        for(let i = 0;i < allChooseCfg.length;i++){
            this.allChooseCfg[allChooseCfg[i].part] = allChooseCfg[i];
        }
        this.initUI();
    },
    initUI(){
        this.curList = [];
        for (let key in this.allChooseCfg) {
            let chooseCfg = this.allChooseCfg[key];
            if(chooseCfg){
                let discountData = Initializer.limitActivityProxy.getDiscountClotheInfo(chooseCfg.id);
                let realCost = (discountData == null)?chooseCfg.price:(chooseCfg.price*discountData.discount*0.1);
                let clotheCfg = localcache.getFilter(localdb.table_userClothe,'id',chooseCfg.clothe_id);
                clotheCfg.goldLeafCost = realCost;
                this.curList.push(clotheCfg);
            }
        }
        this.list.data = this.curList;
        this.onUpdateCost();
    },
    onClickDelete(t, e) {
        let o = e.data;
        this.target.deleteChooseItem(o.part);
        this.allChooseCfg[o.part] = null;
        this.initUI();
    },
    onUpdateCost(){
        let allCost = 0;
        if(this.allChooseCfg){
            for (let key in this.allChooseCfg) {
                let chooseCfg = this.allChooseCfg[key];
                if(chooseCfg){
                    let discountData = Initializer.limitActivityProxy.getDiscountClotheInfo(chooseCfg.id);
                    let realCost = (discountData == null)?chooseCfg.price:(chooseCfg.price*discountData.discount*0.1);
                    allCost += realCost;
                }
            }
        }
        {
            let t, e, o = Initializer.monthCardProxy.getCardData(1);
            allCost = (o != null)?allCost*0.9:allCost;
        }
        this.lblCost.string = Math.floor(allCost);
    },
    onBuy(){
        if(this.allChooseCfg){
            let allBuyID = [];
            for (let key in this.allChooseCfg) {
                let chooseCfg = this.allChooseCfg[key];
                if(chooseCfg){
                    allBuyID.push(chooseCfg.id);
                }
            }
            if(allBuyID.length > 0){
                Initializer.limitActivityProxy.sendClotheExchange(allBuyID,()=>{
                    this.target.resetChooseItem();
                    this.onClickClost();
                });
            }
        }
    },
    onClickClost() {
        Utils.utils.closeView(this);
    },
});
