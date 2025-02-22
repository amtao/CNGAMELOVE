

var renderListItem = require("RenderListItem");
var roleSpine = require("RoleSpine");
var userHeadItem = require("UserHeadItem");
var a = require("TimeProxy");
var l = require("Initializer");

cc.Class({
    extends: renderListItem,

    properties: {
        nameLabel: cc.Label,
        officeLabel: cc.Label,
        levelLabel: cc.Label,
        rankNumNode: cc.Node,
        rankNum: cc.Label,
        rankTopSprite: cc.Sprite,
        rankTopSpriteFrame: [cc.SpriteFrame],
        roleClothes: roleSpine,
        userHead: userHeadItem,
        noRidNode: cc.Node,
        headNode: cc.Node,
        bgSpriteFrame: [cc.SpriteFrame],
        bgSprite: cc.Sprite,
        topIndex:cc.Node,
    },

    // LIFE-CYCLE CALLBACKS:

    // onLoad () {},

    start () {

    },

    showData() {
        var data = this._data;
        if(this.topIndex){
            this.topIndex.active = false;
            this.rankNum.string = "";
        }
        this.levelLabel.string = data.score;
        var o = localcache.getItem(localdb.table_officer, data.level);
        this.nameLabel.string = data.name;
        this.officeLabel.string = o ? o.name: "";
        if (!data.rid) {
            this.noRidNode.active = true;
            this.rankNumNode.active = false;
            this.rankTopSprite.node.active = false;
            this.bgSprite && (this.bgSprite.spriteFrame = this.bgSpriteFrame[3]);
        } else {
            this.showBg(data.rid);
            if(data.rid < 4) {
                this.rankNumNode.active = false;
                this.rankTopSprite.node.active = true;
                this.rankTopSprite.spriteFrame = this.rankTopSpriteFrame[data.rid - 1];
                this.rankNum.string = data.rid;
                if(this.topIndex){
                    this.topIndex.active = true;
                    this.rankNum.string = "";
                    let num1 = cc.find('Num1',this.topIndex);
                    let num2 = cc.find('Num2',this.topIndex);
                    let num3 = cc.find('Num3',this.topIndex);
                    num1 && (num1.active = (data.rid == 1));
                    num2 && (num2.active = (data.rid == 2));
                    num3 && (num3.active = (data.rid == 3));
                }
            } else {
                this.rankNumNode.active = true;
                this.rankTopSprite.node.active = false;
                this.rankNum.string = data.rid;
            }
            this.noRidNode.active = false;
        }

        var headavatar = data.headavatar && data.headavatar.head || 0;
        if (headavatar == 0) {
            this.roleClothes.node.active = true;
            this.roleClothes.setClothes(data.sex, data.job, data.level, data.clothe);
            this.headNode.active = false;
        } else {
            this.headNode.active = true;
            this.roleClothes.node.active = false;
            this.userHead.setUserHead(data.job, data.headavatar);
        }

    },

    onClickItem() {
        var t = this._data;
        t && (t.uid == l.playerProxy.userData.uid ? a.funUtils.openView(a.funUtils.userView.id) : l.playerProxy.sendGetOther(t.uid));
    },

    showBg (rid) {
        if (rid < 4) {
            this.bgSprite && (this.bgSprite.spriteFrame = this.bgSpriteFrame[rid - 1]);
        } else {
            this.bgSprite && (this.bgSprite.spriteFrame = this.bgSpriteFrame[3]);
        }
    }

    // update (dt) {},
});
