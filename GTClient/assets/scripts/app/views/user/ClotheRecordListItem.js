var i = require("RenderListItem");
let Initializer = require("Initializer");
cc.Class({
    extends: i,
    properties: {
    	lblName:cc.Label,
        lblContext: cc.Label,
        lblLock:cc.RichText,
        nodeLock:cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t){
        	this.lblName.string = t.storyname;
        	if (Initializer.clotheProxy.isUnlockClotheAchieve(t.suitid,t.type,t.para)){
        		this.nodeLock.active = false;
        		this.lblContext.string = t.story;
        	}
        	else{
        		this.nodeLock.active = true;
        		this.lblContext.string = "";
        		this.lblLock.string = Initializer.clotheProxy.getUnlockClotheAchieveDes(t.type,t.para);
        	}
        }
    },
});
