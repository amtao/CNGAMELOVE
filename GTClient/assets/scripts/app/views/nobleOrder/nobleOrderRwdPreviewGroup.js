

cc.Class({
    extends: cc.Component,

    properties: {
        rewardItem: cc.Prefab,
        content:cc.Node,
        titleLabel: cc.Label
    },

    // LIFE-CYCLE CALLBACKS:

    // onLoad () {},

    start () {

    },

    showData(list, isSpecial) {
        list.forEach((item) => {
            var itemNode = cc.instantiate(this.rewardItem);
            itemNode.parent = this.content;
            var script = itemNode.getComponent("nobleOrderRwdPreviewRender");
            script.showData(item);
        });
        this.content.getComponent(cc.Layout).updateLayout();
        this.node.height = this.content.height + 17;
        this.titleLabel.string = isSpecial ? i18n.t("GRL_JINGYING_REWARD") : i18n.t("GRL_NORMAL_REWARD");
    }

    // update (dt) {},
});
