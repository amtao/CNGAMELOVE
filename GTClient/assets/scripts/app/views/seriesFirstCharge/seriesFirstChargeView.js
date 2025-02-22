
var n = require("Utils");
var UIUtils = require("UIUtils");
var initializer = require("Initializer");
var timeProxy = require("TimeProxy");
var List = require("List");
var UrlLoad = require("UrlLoad");
cc.Class({
    extends: cc.Component,

    properties: {
        tabNodes: [cc.Node],
        contentNodes: [cc.Node],
        getBtnNode: cc.Node,
        rechargeBtnNode: cc.Node,
        isGotBtn: cc.Node,
        list:List,
        cMoney: cc.Label,
        roleImage:UrlLoad,
        roleSpine:UrlLoad,
        lname:cc.Label,
        imageType: cc.Node,
        abImage: [cc.Node],
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        this.mixy = 70
        this.maxy = 120
        this.selecteds = []
        this.images = []
        let btns = this.node.getChildByName('imagebg').getChildByName('bottomBtns')
        let imagetitles = this.node.getChildByName('imagebg').getChildByName('imagetitles')
        
        for (var i = 0; i < 4; i++) {
            let selected = btns.getChildByName("listBtn"+i).getChildByName("imageSelect");
            this.selecteds.push(selected)
            let image = imagetitles.getChildByName("image"+i);
            this.images.push(image)
            if(i === 0){}else{
                selected.active = false
                image.active = false
            }
        }


        facade.subscribe("SERIES_FIRST_CHARGE_UPDATE", this.onUpdateData, this);
        this.tabIndex = 0;
        this.refShow()
    },
    refShow(){
        //this.lname.node.y = this.tabIndex === 0?this.mixy:this.maxy
        this.showItemList()
        this.showIscMoney()
        this.toggleBtn()
        this.reSrole()
        this.setName()
    },

    setName(){
        let name = ''
        let items = localcache.getList(localdb.table_fuli_fc_ex)[this.tabIndex].firstRwd
        let len = items.length
        for (let i = 0; i < len; i++) {
            let itd = items[i];
            if(itd.kind === 7){
                name = localcache.getItem(localdb.table_hero, itd.id).name;    
            }else if(itd.kind === 99){
                name = localcache.getItem(localdb.table_card, itd.id).name;
            }else if(itd.kind === 95){
                name =  localcache.getItem(localdb.table_usersuit, localcache.getList(localdb.table_fuli_fc_ex)[this.tabIndex].display[0].id).name
            }
        }
        this.lname.string = name
    },

    getSpineUrl (data) {
        var url = "";
        switch (data.kind) {
            case 7:
                url = UIUtils.uiHelps.getServantSpine(data.id);
                //this.showServantInfo(data.id);
                break;
            // case 8:
            //     var res = localcache.getItem(localdb.table_wife, data.id).res;
            //     url = a.uiHelps.getWifeBody(res);
            //     break;
            // case 95:
            //     var i = localcache.getItem(localdb.table_userClothe, data.id).model.split("|");
            //     url = a.uiHelps.getRoleSpinePart(i[0]);
            //     break;
            case 111: 
                let servantDress = localcache.getItem(localdb.table_heroDress, data.id);
                url = UIUtils.uiHelps.getServantSkinSpine(servantDress.model);
            break;
        }
        return url;
    },

    reSrole(){
        let display = localcache.getList(localdb.table_fuli_fc_ex)[this.tabIndex].display
        if(this.tabIndex === 0 && this.roleImage){
            this.roleImage.url = this.getSpineUrl(display[0]);
            this.roleImage.node.active = true
        }else if(this.tabIndex === 3){
            initializer.playerProxy.loadPlayerSpinePrefab(this.roleSpine,{suitId:display[0].id});
            this.roleSpine.node.active = true
        }
    },

    showIscMoney(){
        this.cMoney.string = initializer.seriesFirstChargeProxy.data.money;
    },
    filterexcl(items){
        let len = items.length
        let it = []
        for (let i = 0; i < len; i++) {
            if(items[i].kind === 1){
                it.push(items[i])
            }
        }
        return it
    },
    showItemList(){
        let items = this.filterexcl(localcache.getList(localdb.table_fuli_fc_ex)[this.tabIndex].firstRwd)
        console.log(items)
        this.list.data = items
    },

    start () {

    },

    onUpdateData () {
          this.refShow();
    },

    onClickTab (e, data) {
        let index = parseInt(data);
        if (!data || this.tabIndex === index) return;
        this.tabIndex = index;
        for (var i = 0; i < 4; i++) {
            this.selecteds[i].active = index === i
            this.images[i].active = index === i
            this.abImage[i].active = index === i
        }
        this.refShow()
        return         
        for (var i = 0; i < this.tabNodes.length; i++) {
            var selected = this.tabNodes[i].getChildByName("selected");
            selected.active = i === index;
            this.contentNodes[i].active = i === index;
        }
        
    },

    onClickClose() {
        n.utils.closeView(this);
    },

    onGetBtn () {
        var rechargeId = this.tabIndex + 1;
        initializer.seriesFirstChargeProxy.sendGetReward(rechargeId);
    },

    onRechargeBtn () {
        timeProxy.funUtils.openView(timeProxy.funUtils.recharge.id);
    },

    toggleBtn () {
        var rechargeId = this.tabIndex + 1;
        var isCanGot = initializer.seriesFirstChargeProxy.checkCanGet(rechargeId);
        var isGot = initializer.seriesFirstChargeProxy.checkIsGot(rechargeId);
        this.getBtnNode.active = isCanGot;
        this.rechargeBtnNode.active = !isCanGot && !isGot;
        this.isGotBtn.active = isGot;
    }

    // update (dt) {},
});
