var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("List");
var r = require("Initializer");
var a = require("UIUtils");

cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblJbValue: cc.Label,
        roleUrl: n,
        btnShow: cc.Button,
        list: l,
        nLimit: cc.Node,
        nodeNew: cc.Node,
        lbStoryNum: cc.Label,
    },

    onLoad() {
        this.defaultRolePosY = this.roleUrl.node.position.y;
        this.addBtnEvent(this.btnShow);
    },
    
    showData() {
        var t = this.data;
        if (t && 1 == t.type) {
            var e = localcache.getItem(localdb.table_hero, t.roleid);
            this.lblName.string = e.name;
            var o = r.jibanProxy.getHeroJB(t.roleid);
            this.lblJbValue.string = o + "";
            this.roleUrl.loadHandle = () => {
                this.servantAnchorYPos(this.roleUrl);              
            };
            this.roleUrl.url = a.uiHelps.getServantHead(t.roleid);
            let myCount = 0;
            for (var i = [], n = 1; n <= 3; n++) {
                if(n == 3) {
                    n = 10;
                }
                let count = r.jibanProxy.getJbItemCount(t.roleid, n);
                count > 0 && i.push({ unlocktype: n, num: count});
                myCount += count;
            }         
            this.list.data = i;
            this.nLimit.active = 0 == i.length;
            this.nodeNew.active = r.jibanProxy.hasNewStory(t.roleid);

            let allCount = localcache.getGroup(localdb.table_heropve, "roleid", t.roleid).length;
            this.lbStoryNum.string = i18n.t("COMMON_NEED", { f: myCount, s: allCount });
        }
    },

    servantAnchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultRolePosY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        } 
    },
});
