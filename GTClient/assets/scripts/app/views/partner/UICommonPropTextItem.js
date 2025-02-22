var i = require("RenderListItem");
var l = require("Initializer");
var r = require("UIUtils");
var s = require("List");
var u = require("UrlLoad");
var p = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblpropname1: cc.Label,
        lblpropname2: cc.Label,
        lblpropnum1:cc.Label,
        lblpropnum2:cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var len = t.length;
            for (var ii =0;ii < 2;ii++){
                var ss_ = t[ii];
                var lblname = this[`lblpropname${ii+1}`];
                var lblnum = this[`lblpropnum${ii+1}`]
                if (ss_ != null){
                    lblname.string = ss_.name;
                    lblnum.string = ss_.value;
                }
                else{
                    lblname.string = "";
                    lblnum.string = "";
                }
            }
        }
    },


});
