var i = require("RenderListItem");
var n = require("UrlLoad");
cc.Class({
    extends: i,
    properties: {
        yellowEvent: cc.Sprite,
        redEvent: cc.Sprite,
        yellowNor: cc.Sprite,
        redNor: cc.Sprite,
        end: cc.Sprite,
        begin: cc.Sprite,
        city: cc.Node,
        cityUrl: n,
        cityName: cc.Label,
    },
    ctor() {},
    showData() {},
});
