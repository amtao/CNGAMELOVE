var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var r = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        weaklist: i,
        // img: cc.Sprite,
        // nDenglongBg: cc.Node,
        btnTitles: [cc.Node],
        nodeContents: [cc.Node],
        seColor: cc.Color,
        nonColor: cc.Color,
        //nQiandao: cc.Node,
    },
    ctor() {},
    onLoad() {
        this.updateList();
        this.updataWeakList();
        facade.subscribe(n.welfareProxy.UPDATE_WELFARE_QIANDAO, this.updateList, this);
        facade.subscribe(n.welfareProxy.UPDATE_WELFARE_ZHOUQIAN, this.updataWeakList, this);
        this.onclickTab(null, 1);
    },
    updateList() {
        this.list.data = n.welfareProxy.getDailyList();
        // let bChecked = 0 != n.welfareProxy.qiandao.qiandao;
        // r.shaderUtils.setImageGray(this.img, bChecked);
        // this.nDenglongBg.active = !bChecked;
        // if (0 != n.welfareProxy.qiandao.qiandao) {
        //     this.animation.enabled = !1;
        //     this.animation.play("");
        //     this.animation.stop();
        //     this.nQiandao.active = false;
        // } else{
        //     l.utils.showEffect(this.animation, 0);
        //     this.nQiandao.active = true;
        // } 
    },
    updataWeakList() {
        for (var t = localcache.getList(localdb.table_monday).length, e = [], o = 0; o < t; o++) {
            var i = localcache.getItem(localdb.table_monday, o + 1);
            i && e.push(i);
        }
        e.sort((a, b) => {
            return a.dayid - b.dayid;
        });
        this.weaklist.data = e;
    },
    onClickItem() {
        0 == n.welfareProxy.qiandao.qiandao ? n.welfareProxy.sendQiandao() : l.alertUtil.alert18n("WELFARE_QIANDAO_LIMIT");
    },
    onClickClost() {
        l.utils.closeView(this);
    },
    onclickTab(t, e) {
        if(t && !t.isChecked) {
            return;
        }
        let index = parseInt(e) - 1;
        for (let i = 0; i < this.btnTitles.length; i++) {
            let bShow = i == index;
            // this.btnTitles[i].color = bShow ? this.seColor : this.nonColor;
            this.nodeContents[i].active = bShow;
        }
        switch (index) {
            case 0:
                this.updateList();
                break;
            case 1:
                this.updataWeakList();
                break;
        }
    },
});
