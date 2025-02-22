var Initializer = require("Initializer");
var Utils = require("Utils");
let UrlLoad = require("UrlLoad");
let List = require("List");
let UIUtils = require("UIUtils");
let ShaderUtils = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblcost:cc.Label,
        lblTitleArr:[cc.Label],
        spIconArr:[UrlLoad],
        nodeLimit:cc.Node,
        btnOpen:cc.Button,
        listItemArr:[List],
        nodeChooseArr:[cc.Node],
        nodeOpen:cc.Node,
        nodeEnter:cc.Node,
        lblTips:cc.Label,
        lblLeftTime:cc.Label,
        nodeItemArr:[cc.Node],
        btnEnter:cc.Button,
        lblTips2:cc.Label,
    },
    ctor() {
        this.chooseId = 0;
        this.mState = 0;
        this.endTime = 0;
    },
    onLoad() {
        this.initView();
        this.onClickChoose(null,"1");
        facade.subscribe("UNION_RESOURCEBASE", this.initView, this);
    },

    initView(){
        let state = Initializer.unionProxy.partyState();
        this.mState = state;
        for (let ii = 0; ii < this.lblTitleArr.length;ii++){
            let cfg = localcache.getItem(localdb.table_party,ii+1);
            this.lblTitleArr[ii].string = cfg.name;
            this.listItemArr[ii].data = cfg.club_rwd.concat(cfg.personal_rwd);
            this.spIconArr[ii].url = UIUtils.uiHelps.getXunfangIcon(cfg.icon);
            if (state == 1){
                if (Initializer.bagProxy.getItemCount(Initializer.unionProxy.banquetId) >= cfg.cost){                
                    ShaderUtils.shaderUtils.clearNodeShader(this.nodeItemArr[ii]);
                }
                else{
                    ShaderUtils.shaderUtils.setNodeGray(this.nodeItemArr[ii]);
                }
            }
            else{
                if (Initializer.unionProxy.partyResourceData.partyLv == ii + 1){
                    ShaderUtils.shaderUtils.clearNodeShader(this.nodeItemArr[ii]);
                }
                else{
                    ShaderUtils.shaderUtils.setNodeGray(this.nodeItemArr[ii]);
                }
            }
        }
        this.btnEnter.interactable = true;
        if (state == 1){
            this.nodeOpen.active = true;
            this.nodeEnter.active = false;
            let data = Initializer.unionProxy.memberInfo;
            if (data.post == 1 || data.post == 2){
                this.nodeLimit.active = false;
                this.btnOpen.node.active = true;
                this.lblcost.node.parent.active = true;
            }
            else{
                this.nodeLimit.active = true;
                this.btnOpen.node.active = false;
                this.lblcost.node.parent.active = false;
            }
        }
        else if(state == 2){
            this.nodeOpen.active = false;
            this.nodeEnter.active = true;
            this.lblTips2.node.active = false;
            this.lblTips.node.active = true;
            this.lblTips.string = i18n.t("UNION_TIPS34");
            this.btnEnter.interactable = false;
            let self = this;
            UIUtils.uiUtils.countDown(Initializer.unionProxy.getOpenOrEndTime(true),this.lblLeftTime,function(){
                self.initView();
            })
            this.onClickChoose(null,Initializer.unionProxy.partyResourceData.partyLv)
        }
        else if(state == 3){
            this.nodeOpen.active = false;
            this.nodeEnter.active = true;
            this.lblTips.node.active = true;
            this.lblTips2.node.active = false;
            this.lblTips.string = i18n.t("UNION_TIPS35");
            let self = this;
            UIUtils.uiUtils.countDown(Initializer.unionProxy.getOpenOrEndTime(false),this.lblLeftTime,function(){
                self.initView();
            })
            this.onClickChoose(null,Initializer.unionProxy.partyResourceData.partyLv)
        }
        else if(state == 4){
            this.nodeOpen.active = false;
            this.nodeEnter.active = true;
            this.lblLeftTime.string = "";
            this.lblTips.node.active = false;
            this.lblTips2.node.active = true;
        }
        for (let item of this.nodeChooseArr){
            item.active = false;
        }
    },

    onClose() {
        Utils.utils.closeView(this);
    },

    /**开启宴会*/
    onClickOpen() {
        let index = this.chooseId
        let cfg = localcache.getItem(localdb.table_party,index);      
        let id = Initializer.unionProxy.banquetId;
        let itemcfg = localcache.getItem(localdb.table_item,id);
        Utils.utils.showConfirmItem(
                i18n.t("UNION_TIPS33",{v1:`${itemcfg.name}x${cfg.cost}`,v2:cfg.name}),
                id,
                Initializer.bagProxy.getItemCount(id),
                function() {
                    if (Initializer.bagProxy.getItemCount(id) < cfg.cost){
                        Utils.alertUtil.alert(i18n.t("COMMON_LIMIT",{n:itemcfg.name}));
                        return;
                    }
                    Initializer.unionProxy.sendOpenParty(index,function () {
            
                    });
                },
                "COMMON_YES"
            );   
    },

    onClickChoose(t,idx){
        if (this.mState != 1) return;
        let index = Number(idx);
        this.chooseId = index;
        for (let ii = 0; ii < this.nodeChooseArr.length;ii++){
            this.nodeChooseArr[ii].active = ii == (index-1);
        }
        let cg = localcache.getItem(localdb.table_party,index);
        this.lblcost.string = i18n.t("COMMON_NUM",{f:Initializer.bagProxy.getItemCount(Initializer.unionProxy.banquetId),s:cg.cost});
        this.lblcost.node.color = Initializer.bagProxy.getItemCount(Initializer.unionProxy.banquetId) >= cg.cost ? Utils.utils.GREEN : Utils.utils.RED
    },

    onClickEnter(){
        if (this.mState == 2){
            Utils.alertUtil.alert18n("UNION_TIPS40");
            return;
        }
        else if(this.mState == 4){
            Utils.alertUtil.alert18n("UNION_TIPS41");
            return;
        }
        Initializer.unionProxy.sendJoinParty(function(){
            Utils.utils.openPrefabView("union/UIUnionPartyMain");
        })
        this.onClose();       
    },

});
