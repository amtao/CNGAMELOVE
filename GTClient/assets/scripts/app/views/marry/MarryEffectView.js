var i = require("Utils");
var n = require("Initializer");
var l = require("ChildSpine");
var r = require("ServantStarShow");
var a = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName1:cc.Label,
        lblFather1:cc.Label,
        lblShuXing1:cc.Label,
        lblName2:cc.Label,
        lblFather2:cc.Label,
        lblShuXing2:cc.Label,
        roel1:l,
        roel2:l,
        lblSf1:cc.Label,
        lblSf2:cc.Label,
        stars1:r,
        stars2:r,
        list1:a,
        list2:a,
    },

    onLoad() {
        var t = this.node.openParam;
        if (t) {
            var e = n.sonProxy.getSon(t.sid || t.id);
            if (e) {
                this.lblName1.string = e.name;
                localcache.getItem(localdb.table_wife, e.mom);
                this.lblFather1.string = n.playerProxy.userData.name;
                var o = e.ep.e1 + e.ep.e2 + e.ep.e3 + e.ep.e4;
                this.lblShuXing1.string = o + "";
                this.lblName2.string = e.spouse.sname;
                this.lblFather2.string = e.spouse.fname;
                var i = e.spouse.ep.e1 + e.spouse.ep.e2 + e.spouse.ep.e3 + e.spouse.ep.e4;
                this.lblShuXing2.string = i + "";
                this.roel1.setMarry(e.id, e.sex);
                this.roel2.setMarry(e.spouse.sonuid, e.spouse.sex);
                this.lblSf1.string = n.sonProxy.getHonourStr(e.honor);
                this.lblSf2.string = n.sonProxy.getHonourStr(e.spouse.honor);
                this.stars1.setValue(e.talent);
                this.stars2.setValue(e.spouse.talent);
                this.list1.node.x = -this.list1.node.width / 2;
                this.list2.node.x = -this.list2.node.width / 2;
            }
        }
    },
    onClickClose() {
        if(this.node.openParam.ttt){
            i.utils.closeView(this);
            return
        }
        facade.send("MARRY_EFFECT_END");
        i.utils.closeView(this);
    },
});
