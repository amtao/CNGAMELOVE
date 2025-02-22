
cc.Class({
    extends:cc.Component,

    properties: {

    },

    ctor(){
        
    },

    onLoad : function () {
        facade.subscribe("DOWN_OVER", this.onDownOver, this);
    },

    onDownOver : function(data){
    	
    }

});