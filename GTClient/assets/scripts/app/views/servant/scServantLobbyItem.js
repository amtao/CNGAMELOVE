let urlLoad = require("UrlLoad");
let scUtils = require("UIUtils");
let utils = require("Utils");
let initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        servantShow: urlLoad,
        nServantShow: cc.Node,
        lbName: cc.Label,
        nLock: cc.Node,
        nRed: cc.Node,
        id: 0,
    },

    setData: function(cfgData, bHas, index) {
        this._data = cfgData;
        this.bHas = bHas;
        let self = this;
        this.scheduleOnce(() => {
            self.servantShow.url = scUtils.uiHelps.getServantSpine(self.id);
            self.scheduleOnce(()=> {
                self.setStatus(self.nServantShow, bHas);
            }, 0.2);
        }, (index + 1) * 0.1); 
        this.lbName.string = cfgData.name;
        this.nLock.active = !bHas;      
        if(bHas) {
            let proxy = initializer.servantProxy;
            let data = proxy.servantMap[this.id];
            let jibanLevelData = initializer.jibanProxy.getHeroJbLv(this.id);   
            this.nRed.active = proxy.getLevelUp(data) || proxy.getTanlentUp(data) || proxy.getSkillUp(data) || proxy.isCanTiBa(data)
             || proxy.dicTokenRed[data.id] || proxy.servantJiBanRoadRed[data.id]
             || (null != proxy.inviteBaseInfo && proxy.inviteBaseInfo.inviteCount > 0 && (jibanLevelData.fish == 1 || jibanLevelData.food == 1));
        } else {
            this.nRed.active = false;
        }
    },

    setStatus: function(node, bHas) {
        if(node.childrenCount > 0) {
            let child = node.children[0];
            //node.width = child.width * 0.7;
            node.height = child.height;
            let spines = child.getComponentsInChildren(sp.Skeleton);
            for(let i = 0, len = spines.length; i < len; i++) {
                let spine = spines[i];
                spine.node.color = bHas ? cc.Color.WHITE : cc.Color.GRAY;
                spine.paused = !bHas;
            }
        } else {
            let self = this;
            this.scheduleOnce(() => {
                self.setStatus(node, bHas);
            }, 0.2);
        }
    },

    onClickServant: function() {
        if(this.bHas) {
            utils.utils.openPrefabView("servant/ServantView", !1, {
                hero: this._data,
                tab: 4
            });
            utils.utils.closeNameView("servant/ServantListView", !1);
        } else {
            utils.utils.openPrefabView("servant/ServantNoView", !1, this._data);
            utils.utils.closeNameView("servant/ServantListView", !1);
        }        
    },

});
