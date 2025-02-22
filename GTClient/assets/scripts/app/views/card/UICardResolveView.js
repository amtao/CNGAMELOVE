let Utils = require("Utils");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Initializer = require("Initializer");
var ItemSlotUI = require("ItemSlotUI");
var List = require("List");
var Tip = require("Tip");
var GoldShow = require("GoldShow");
cc.Class({
    extends: cc.Component,

    properties: {
        norColor:cc.Color,
        secColor:cc.Color,
        check: cc.Toggle,
        btnArr:[cc.Button],
        nodeSelectArr:[cc.Node],
        btnTitleArr:[cc.Label],
        itemBig:ItemSlotUI,
        itemSmall:ItemSlotUI,
        itemBigCoin:ItemSlotUI,
        itemSmallCoin:ItemSlotUI,
        listView:List,
        nodeNull:cc.Node,
        tip:Tip,
        goldShow:GoldShow,
        effectBigSpine:sp.Skeleton,
        nodeMask:cc.Node,
        nodeResolveBtn:cc.Node,
        nodeBlack:cc.Node,
    },

    ctor(){
        this.chooseIdx = 0;
        this.chooseAllFlag = false;
        this.chooseCardDic = {};
        this.listData = [];
        this.pool = [];
    },
    onLoad(){
        facade.subscribe("CARD_RESLOVE_ADDANDMINU", this.onChooseCard, this);
        this.nodeMask.active = false;        
        this.onClickTab(null,1);
        this.effectBigSpine.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName === 'animation') {
                this.effectBigSpine.animation = "";
                this.effectBigSpine.node.active = false;
            }
        })
    },
    onClickBack() {
        Utils.utils.closeView(this);
    },

    onClickCheck() {
        this.chooseAllFlag = this.check.isChecked;
        for (var ii = 0; ii < this.listData.length;ii++){
            let cg = this.listData[ii];
            this.listData[ii].currentCount = this.check.isChecked ? cg.count + 0 : 0;
        }
        this.listView.data = this.listData;
        this.onShowDetail();
    },


    /**点击分解*/
    onClickResolve(){
        let str = "";
        for (var ii = 0; ii < this.listData.length;ii++){
            let cg = this.listData[ii];
            if (cg.currentCount > 0){
                str += `${cg.id},${cg.currentCount}|`;
            }
        }
        if (str == ""){
            Utils.alertUtil.alert(i18n.t("CARD_NULL_RESOLVE"));
            return;
        }
        str = str.substring(0, str.length - 1);
        let self = this;
        Initializer.cardProxy.sendCardDecompose(str,()=>{
            self.onBeganAni();
        });
    },

    /**开始动画*/
    onBeganAni(){
        this.nodeBlack.active = false;
        this.nodeMask.active = true;
        let self = this;
        let tmpIdx = 0;
        var count = this.listView.node.childrenCount;
        this.unscheduleAllCallbacks();
        this.scheduleOnce(()=>{
            if (self.node == null) return;
            self.nodeBlack.active = true;
            self.effectBigSpine.animation = "";
            self.effectBigSpine.node.active = true;
            self.effectBigSpine.animation = "animation";
        },0.7)
               
        for (var ii = 0; ii < count;ii++){
            let child = this.listView.node.children[ii];
            if (child && child.active && child.getComponent("cardResolveItem").data.currentCount > 0){
                tmpIdx++;
                child.getComponent("cardResolveItem").onBeganDissolve(()=>{
                    tmpIdx--;
                    if (tmpIdx <= 0){
                        self.exchangeButton();
                        Initializer.timeProxy.floatReward();
                        self.nodeMask.active = false;
                        self.nodeBlack.active = false;
                    }
                });
            }
        }
    },

    exchangeButton(){
        for (var i = 0; i < this.btnArr.length; i++) {
            let flag = this.chooseIdx == i;
            let btn = this.btnArr[i];
            btn.interactable = !flag;
            this.nodeSelectArr[i].active = flag;
            this.btnTitleArr[i].color = flag ? this.secColor : this.norColor;
        }
        this.check.isChecked = false;
        let listdata = [];
        let cardListCfg = localcache.getGroup(localdb.table_card,"quality",this.chooseIdx + 1);
        if (cardListCfg){
            for (var ii = 0; ii < cardListCfg.length;ii++){
                let cg = cardListCfg[ii];
                let num = Initializer.bagProxy.getItemCount(cg.item);
                if (num > 0){
                    listdata.push({id:cg.item,count:num,currentCount:this.check.isChecked ? num : 0});
                }
            }
        }       
        this.listView.data = listdata;
        this.listData = listdata;
        this.nodeNull.active = listdata.length == 0;
        this.nodeResolveBtn.active = this.check.node.active = listdata.length > 0;
        this.onShowDetail();
        this.goldShow.onDelayRefresh();
    },

    onClickTab(t,e){
        this.chooseIdx = Number(e) - 1;
        let cfg = localcache.getItem(localdb.table_card_decompose,e);
        this.goldShow.onRefreshRes(cfg.item[0].itemid);
        this.tip.onRefreshItem(cfg.item[0].itemid);
        this.itemBig.data = {id:cfg.item[0].itemid,kind:1,count:1};
        this.itemSmall.data = {id:cfg.item[1].itemid,kind:1,count:1};
        this.itemBigCoin.data = {id:cfg.item[0].itemid,kind:1,count:0};
        this.itemSmallCoin.data = {id:cfg.item[1].itemid,kind:1,count:0};
        this.exchangeButton();
    },

    onChooseCard(data){
        for (var ii = 0; ii < this.listData.length;ii++){
            let cg = this.listData[ii];
            if (cg.id == data.id){
                this.listData[ii].currentCount = data.count + 0;
                break;
            }
        }
        this.onShowDetail();
    },

    onShowDetail(){
        let tmpList = []        
        for (var ii = 0; ii < this.listData.length;ii++){
            let cg = this.listData[ii];
            if (cg.currentCount > 0){
                let cardCfg = localcache.getItem(localdb.table_card,cg.id);
                let cardDecomposeCfg = localcache.getItem(localdb.table_card_decompose,cardCfg.quality);
                for (var jj = 0; jj < cardDecomposeCfg.item.length;jj++){
                    let cc = cardDecomposeCfg.item[jj];
                    if (tmpList[jj] == null){
                        tmpList.push({kind:cc.kind,count:cc.count*cg.currentCount,id:cc.itemid})
                    }
                    else{
                        tmpList[jj].count += (cc.count*cg.currentCount);
                    }
                }
            }
        }
        let listCoinItem = [this.itemBigCoin,this.itemSmallCoin];
        for (var ii = 0; ii < 2; ii++){
            if (tmpList[ii] == null){
                let data = listCoinItem[ii].data;
                data.count = 0;
                listCoinItem[ii].data = data;
            }
            else{                
                listCoinItem[ii].data = tmpList[ii];
            }
        }
    }

    
});
