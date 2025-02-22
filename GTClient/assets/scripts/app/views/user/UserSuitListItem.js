var i = require("RenderListItem");
var n = require("UrlLoad");
var UIUtils = require("UIUtils");
var Initializer = require("Initializer");
var Utils = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        icon: n,
        cardSlotArr:[n],
        nodeMask:cc.Node,
        nodeSlotContent:cc.Node,
        nodeRed:cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.name;
            this.icon.url = UIUtils.uiHelps.getRolePart(t.icon);
            let flag = true;
            for (let ii = 0; ii < t.clother.length;ii++){
                if (!Initializer.playerProxy.isUnlockCloth(t.clother[ii])){
                    flag = false;
                    break;
                }
            }
            this.nodeMask.active = !flag;
            this.nodeSlotContent.active = flag;
            if (flag){
                for (let ii = 0; ii < this.cardSlotArr.length;ii++){
                    if (Initializer.clotheProxy.IsCardSlotActive(t.id,ii+1)){
                        this.cardSlotArr[ii].url = UIUtils.uiHelps.getUserclothePic("sz_bg_yf1")
                    }
                    else if(Initializer.clotheProxy.isHasCardInSlot(t.id,ii+1)){
                        this.cardSlotArr[ii].url = UIUtils.uiHelps.getUserclothePic("sz_bg_yf2")
                    }
                    else{
                        this.cardSlotArr[ii].url = UIUtils.uiHelps.getUserclothePic("sz_bg_yf3")
                    }
                    
                }
                this.nodeRed.active = Initializer.clotheProxy.isCanActiveCut(t.id);
            }
        }
    },

    onClickItem(){
        Utils.utils.openPrefabView("user/UserSuitDetail", !1, { cfg: this._data });
    },
});
