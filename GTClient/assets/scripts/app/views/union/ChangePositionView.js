var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        imgBg: cc.Node,
    },
    ctor() {
        this.dlist = [];
    },
    onLoad() {

        if(!n.unionProxy.changePosParam){
            console.error('changePosParam is undifind')
            return
        }
        
        var t = n.unionProxy.getUnionData(n.unionProxy.clubInfo.level)
        e = [0, 1, t.leader, t.elite];
        1 == n.unionProxy.memberInfo.post && this.dlist.push(this.getPosData(2, e));
        this.dlist.push(this.getPosData(4, e));
        this.btnsc = []
        this.btns = []
        let len = 2 
        for (let i = 0; i < len; i++) {
            let mod = {}
            let btn = this.imgBg.getChildByName('btn'+i)
            let btnvlaout = btn.getChildByName('vlaout')
            mod.posts = btnvlaout.getChildByName('posts').getComponent(cc.Label)
            mod.left = btnvlaout.getChildByName('left').getComponent(cc.Label)
            mod.right = btnvlaout.getChildByName('right').getComponent(cc.Label)
            mod.numbes = btnvlaout.getChildByName('numbes').getComponent(cc.Label)
            mod.now = btnvlaout.getChildByName('now').getComponent(cc.Label)
            if(1 != n.unionProxy.memberInfo.post && i==0){
                btn.active = false
            }else{
                this.btnsc.push(mod)
                this.btns.push(btn)
            }
        }

        len = this.dlist.length
        for (let i = 0; i < this.dlist.length; i++) {
            let pl = this.dlist[i]
            //this.btns[i].active = pl.active === 1
            this.btnsc[i].posts.string = pl.name
            this.btnsc[i].numbes.node.active = pl.index === 2
            this.btnsc[i].left.node.active = pl.index === 2
            this.btnsc[i].right.node.active = pl.index === 2
            this.btnsc[i].now.node.active = pl.iss === 1
            this.btnsc[i].numbes.string = pl.namePostn
        }
    },
    getPosData(t, e) {
        var o = n.unionProxy.getPostNum(t,n.unionProxy.changePosParam.id),
        i = e.length > t ? e[t] : 0,
        l = {};
        l.name = i18n.t("UNION_POSITION_" + t);
        l.namePostn = o + "/" + i //(0 != i ? "(" + o + "/" + i + ")": "")
        l.active = 4 == t || o < i ? 1 : 0;
        l.pos = o;
        l.index = t;
        l.iss = n.unionProxy.getPostIsPp(t,n.unionProxy.changePosParam.id)
        return l;
    },
    eventClose() {
        l.utils.closeView(this);
    },

    onClickChange(t, e) {
        var o = parseInt(e);
        o && n.unionProxy.sendChangePos(n.unionProxy.changePosParam.id, o);
        this.eventClose();
    },
});
