var i = require("Utils");
var n = require("ChildSpine");
cc.Class({
    extends: cc.Component,
    properties: {
        childSpine: n,
        nameText: cc.Label,
        mother: cc.Label,
        total: cc.Label,
        wuli: cc.Label,
        zhili: cc.Label,
        zz: cc.Label,
        meili: cc.Label,
        lblSf: cc.Label,
        animSp1:sp.Skeleton,
        animSp2:sp.Skeleton,
        animSp3:sp.Skeleton,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            this.nameText.string = t.name;
            this.wuli.string = "" + t.ep.e1;
            this.zhili.string = "" + t.ep.e2;
            this.zz.string = "" + t.ep.e3;
            this.meili.string = "" + t.ep.e4;
            var e = t.ep.e1 + t.ep.e2 + t.ep.e3 + t.ep.e4;
            this.total.string = "" + e;
            this.childSpine.setKid(t.id, t.sex);
            var o = localcache.getItem(localdb.table_adult, t.honor);
            this.lblSf.string = o.name;
        }

        //动画监听
        this.animSp1.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName === 'tudi_on') {
                this.animSp1.setAnimation(0, 'tudi_idle', true);
            }     
        });  
        this.animSp2.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName === 'tudi_on') {
                this.animSp2.setAnimation(0, 'tudi_idle', true);
            }     
        }); 
        this.animSp3.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName === 'tudi_on') {
                this.animSp3.setAnimation(0, 'tudi_idle', true);
            }     
        }); 
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
