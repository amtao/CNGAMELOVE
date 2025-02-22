let Utils = require("Utils");
let TimeProxy = require("TimeProxy");
let Initializer = require("Initializer");
let List = require("List");
var UrlLoad = require("UrlLoad");
var ItemSlotUI = require("ItemSlotUI");
var UIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        btnArr:[cc.Button],
        lblArr:[cc.Label],
        nodeChooseArr:[cc.Node],
        nodeNull:cc.Node,
        nodeHas:cc.Node,
        nodeAchieve:cc.Node,
        nodeNormal:cc.Node,
        listView:List,
        listAchieve:List,
        seColor: cc.Color,
        norColor: cc.Color,
        lblName:cc.Label,
        icon:UrlLoad,
        lblDes:cc.Label,
        lblCollect:cc.Label,
        nodeGet1:cc.Node,
        nodeGet2:cc.Node,
        progress:cc.ProgressBar,
        item1:ItemSlotUI,
        item2:ItemSlotUI,
        lblProgressbarValue:cc.Label,
        nodeGot1:cc.Node,
        nodeGot2:cc.Node,
        lblMaxScore:cc.Label,
        lblComplete:cc.Label,
        btnGet1:cc.Button,
        btnGet2:cc.Button,
        lblMaxGet:cc.Label,
        lblTitle:cc.Label,
    },

    ctor(){
        this.mType = 0;
        this.listItemData = [];
        this.chooseId = 0;
    },

    onLoad: function() {
        facade.subscribe("UPDATE_FENGWUZHI_ACHIEVE", this.updateAchieveList, this);
        facade.subscribe("CHOOSE_FENGWUZHI_ITEM",this.onChooseItem,this);
        
        facade.subscribe("UPDATE_COLLECT_BASEINFO", this.updateItemDetail, this);
        this.onExchangeButton(null,"0");
        Initializer.miniGameProxy.sendGetCollectInfo();
    },

    onChooseItem(data){
        let id = data.id;
        for (var ii = 0; ii < this.listItemData.length;ii++){
            let cg = this.listItemData[ii];
            this.listItemData[ii].isChoose = cg.cfg.id == id;
        }
        this.listView.updateRenders();
        this.onShowDetail(id);
    },

    onShowDetail(id){
        let cData = Initializer.servantProxy.collectAwardInfo;
        this.nodeNull.active = false;
        this.nodeHas.active = false;
        if (cData[String(id)] == null){
            this.nodeNull.active = true;
            return;
        }
        this.chooseId = id + 0;
        this.nodeHas.active = true;
        this.nodeGet1.active = false;
        this.nodeGet2.active = false;
        this.nodeGot1.active = false;
        this.nodeGot2.active = false;
        this.btnGet1.interactable = false;
        this.btnGet2.interactable = false;
        let itemcfg = localcache.getItem(localdb.table_game_item,id);       
        let data = cData[String(id)];
        this.lblName.string = itemcfg.name;
        this.lblDes.string  = itemcfg.txt;
        this.icon.url = UIUtils.uiHelps.getItemSlot(id);
        this.lblCollect.string = i18n.t("FISH_TIPS27",{v1:data.num});
        let cfg = localcache.getFilter(localdb.table_collection_rwd,"rid",data.rwd+1,"type",id);
        if (cfg){
            this.item2.data = cfg.rwd[0];
            let value = data.num / cfg.need;
            this.progress.progress = value > 1 ? 1 : value;
            this.lblProgressbarValue.string = i18n.t("COMMON_NUM",{f:data.num,s:cfg.need});
            this.nodeGet2.active = true;          
            if (value >= 1){                
                this.btnGet2.interactable = true;
            }
            else{
                this.btnGet2.interactable = false;
            }
        }
        else{
            cfg = localcache.getFilter(localdb.table_collection_rwd,"rid",data.rwd,"type",id);
            this.item2.data = cfg.rwd[0];
            this.progress.progress = 1;
            this.lblProgressbarValue.string = i18n.t("COMMON_NUM",{f:data.num,s:cfg.need});       
            this.nodeGot2.active = true;
        }
        let maxScore = Initializer.servantProxy.collectInfo.maxScore;
        
        let maxRwdCfg = localcache.getItem(localdb.table_max_rwd,id);
        this.item1.data = maxRwdCfg.rwd[0];
        if (maxScore[String(id)] && maxScore[String(id)].score >= maxRwdCfg.maxweight){
            //this.nodeGet1.active = maxScore[String(id)].pick == 0;
            this.nodeGot1.active = maxScore[String(id)].pick == 1;
            this.nodeGet1.active = !this.nodeGot1.active
            this.btnGet1.interactable = maxScore[String(id)].pick == 0;
        }
        else{
            this.nodeGet1.active = true;
            this.btnGet1.interactable = false;
        } 
        if (itemcfg.type == 1){
            if (maxScore[String(id)])
                this.lblMaxScore.string = i18n.t("FISH_TIPS29",{v1:maxScore[String(id)].score});
            this.lblTitle.string = i18n.t("FISH_TIPS31");
            this.lblMaxGet.string = i18n.t("FISH_TIPS33",{v1:maxRwdCfg.maxweight});
        }
        else{
            if (maxScore[String(id)])           
                this.lblMaxScore.string = i18n.t("FISH_TIPS28",{v1:maxScore[String(id)].score});
            this.lblTitle.string = i18n.t("FWZ_MAX_RWD");
            this.lblMaxGet.string = i18n.t("FISH_TIPS32",{v1:maxRwdCfg.maxweight});
        }
    },

    onExchangeButton(t,e){
        let idx = Number(e);
        this.mType = idx;
        for (var ii = 0; ii < this.btnArr.length;ii++){
            let flag = (ii == idx);
            this.btnArr[ii].interactable =  !flag;
            this.nodeChooseArr[ii].active = flag;
            this.lblArr[ii].node.color = flag ? this.seColor : this.norColor;
        }
        this.nodeAchieve.active = false;
        this.nodeNormal.active = false;
        if (idx == 0){
            this.nodeAchieve.active = true;
        }
        else if(idx == 1 || idx == 2){
            this.nodeNormal.active = true;
            let listdata = localcache.getFilters(localdb.table_game_item, "type",idx);
            this.listItemData.length = 0;
            this.nodeHas.active = false;
            this.nodeNull.active = false;
            this.chooseId = 0;
            let index = 0;
            for (var ii = 0; ii < listdata.length;ii++){
                if (listdata[ii].id == 30000) continue;
                index++;
                if (index == 1){
                    this.listItemData.push({cfg:listdata[ii],isChoose:true})
                    this.onShowDetail(listdata[ii].id)
                }
                else
                    this.listItemData.push({cfg:listdata[ii],isChoose:false})
            }
            this.listView.data = this.listItemData;
        }
    },

    /**刷新成就列表*/
    updateAchieveList(){
        if (this.mType == 0){
            let listdata = [];
            let listcfg = localcache.getList(localdb.table_collection_achieve);
            let maxNum = 0;
            let num = 0;
            let taskInfo = Initializer.miniGameProxy.achieveInfo.taskInfo;
            for (var ii = 0; ii < listcfg.length;ii++){
                maxNum++;
                let cg = listcfg[ii];
                let isType = 1;
                if (cg != null && taskInfo[String(cg.id)] && cg.need[cg.need.length-1] <= taskInfo[String(cg.id)].count){
                    num++;
                    if (taskInfo[String(cg.id)].isPick == 1){
                        isType = 0;
                    }
                    else
                        isType = 2;
                }
                listdata.push({cfg:cg,info:taskInfo[String(cg.id)],isType:isType});
            }
            listdata.sort((a,b)=>{
                if (a.isType == b.isType){
                    return a.cfg.id < b.cfg.id ? -1 : 1;
                }
                else{
                    return a.isType > b.isType ? -1 : 1;
                }
            })
            this.listAchieve.data = listdata;
            this.lblComplete.string = i18n.t("FWZ_ACHIEVEMENT",{num1:num,num2:maxNum});
        }
    },

    updateItemDetail(){
        if (this.chooseId != 0){
            this.listView.updateRenders();
            this.onShowDetail(this.chooseId);
        }
    },

    onClickBack: function() {
        Utils.utils.closeView(this, !0);
    },

    onClickCollectAward(){
        Initializer.miniGameProxy.sendPickCollectAward(this.chooseId);
    },

    onClickMaxAward(){
        Initializer.miniGameProxy.sendPickMaxAward(this.chooseId);
    },
});
