
cc.Class({
    extends: cc.Component,

    properties: {
        nSelected: cc.Node,
        nRight: cc.Node,
        nWrong: cc.Node,
        lbAnswer: cc.Label,
        btnSelf: cc.Button,
    },

    setAnswer: function(index, answer) {
        this.nSelected.active = false;
        this.nRight.active = false;
        this.nWrong.active = false;
        this.index = index;
        this.lbAnswer.string = answer;
    },

    setRight: function(bRight) {
        this.nRight.active = bRight;
        this.nWrong.active = !bRight;
    },
});
