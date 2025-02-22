let List = require("List");
let Utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        content: List,
    },
    onLoad () {
        this.data = this.node.openParam;
        this.onUpdateData();
        facade.subscribe("ACHIEVEMENT_UPDATE", this.onUpdateData, this);
    },
    onUpdateData (newData) {
        if(newData){
            this.data = newData;
        }
        if (this.data) {
            this.content.data = this.data.rwd;
        }
    },
    checkCanGet(type,num){
        switch (type) {
            case 1:{
                return this.data.play >= num;
            }break;
            case 2:{
                return this.data.maxCons >= num;
            }break;
            case 3:{
                return this.data.shake >= num;
            }break;
            case 4:{
                return this.data.rank == 1;
            }break;
            case 5:{
                return this.data.taozhuang == 1;
            }break;
        }
        return false;
    },
    getProcessInfo(type){
        switch (type) {
            case 1:{
                return i18n.t('ACTIVITY_GRID_BONUS_TIP7')+this.data.play;
            }break;
            case 2:{
                return i18n.t('ACTIVITY_GRID_BONUS_TIP7')+this.data.maxCons;
            }break;
            case 3:{
                return i18n.t('ACTIVITY_GRID_BONUS_TIP7')+this.data.shake;
            }break;
            case 4:{
                return i18n.t('ACTIVITY_GRID_BONUS_TIP8');
            }break;
            case 5:{
                return i18n.t('ACTIVITY_GRID_BONUS_TIP8');
            }break;
        }
        return "";
    },
    onClickClose () {
        Utils.utils.closeView(this);
    },
});
