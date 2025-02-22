
let Initializer = require("Initializer");
let List = require("List");
let Utils = require("Utils");
let ShaderUtils = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        spBacArr:[cc.Sprite],
        lblSkillArr:[cc.RichText],
        activeItem:cc.Node,
    },
    ctor() {},
    onLoad() {
        let openParam = this.node.openParam;
        let suitid = openParam.suitid;
        for (let ii = 0; ii < this.spBacArr.length;ii++){
            let cg = Initializer.clotheProxy.getActiveCardSlotDes(suitid,ii+1,true);
            let lb = this.lblSkillArr[ii];
            if (cg.isMax){
                ShaderUtils.shaderUtils.setImageGray(this.spBacArr[ii],false);
                lb.string = cg.curDes;
            }
            else if(cg.curDes == null){
                ShaderUtils.shaderUtils.setImageGray(this.spBacArr[ii],true);
                lb.string = `<color=#595959>${cg.nextDes}</color>`;
            }
            else{
                ShaderUtils.shaderUtils.setImageGray(this.spBacArr[ii],false);
                lb.string = cg.nextDes;
            }
        }
        this.activeItem.active = false;
        let listdata = Initializer.clotheProxy.getActiveSlotListDes(suitid);
        let recordList = [this.activeItem];
        for (let ii = 0; ii < listdata.length;ii++){
            let item = recordList[ii]
            if (item == null){
                item = cc.instantiate(this.activeItem);
                this.activeItem.parent.addChild(item);
            }
            item.active = true;
            let lb = item.getChildByName("New Label");
            lb.getComponent(cc.RichText).string = listdata[ii];
        }
    },

    onClickClost() {
        Utils.utils.closeView(this, !0);
    },
});
