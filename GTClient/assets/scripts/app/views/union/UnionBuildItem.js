var i = require("RenderListItem");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var Utils = require("Utils");
var UrlLoad = require("UrlLoad");
import { CLUP_BUILD_TYPE } from "GameDefine";
cc.Class({
    extends: i,
    properties: {
        btn:cc.Button,
        lblLevel:cc.Label,
        lblFund:cc.Label,
        lblDes:cc.Label,
        lblBuildLv:cc.RichText,
        icon:UrlLoad,
    },
    
    onClickBuild() {
        if(null == this._data) 
            return;
        var t = this._data.key;
        let cfg = null;
        let name = ""
        switch(t){
            case CLUP_BUILD_TYPE.LI_SHI_JIAN:{
                cfg = localcache.getFilter(localdb.table_building_up,"building_type",t,"lv",Initializer.unionProxy.clubInfo.lsjLv)
                name = i18n.t("UNION_BUILDING_2");
            }
            break;
            case CLUP_BUILD_TYPE.SHANG_FANG:{
                cfg = localcache.getFilter(localdb.table_building_up,"building_type",t,"lv",Initializer.unionProxy.clubInfo.spLv)
                name = i18n.t("UNION_BUILDING_5");
            }
            break;
            case CLUP_BUILD_TYPE.JIANYAN_TANG:{
                cfg = localcache.getFilter(localdb.table_building_up,"building_type",t,"lv",Initializer.unionProxy.clubInfo.jytLv)
                name = i18n.t("UNION_BUILDING_4");
            }
            break;           
        }
        let itemcfg = localcache.getItem(localdb.table_item,cfg.cost[0].id)
        Utils.utils.showConfirm(i18n.t("UNION_TIPS13",{v1:"" + cfg.cost[0].count + itemcfg.name,v2:name}), () => {
            Initializer.unionProxy.sendCluBuildingUp(t);
        });
        
    },
    showData() {
        if(null == this._data) 
            return;
        var t = this._data.key;
        if (t) {
            let cfg = null;
            switch(t){
                case CLUP_BUILD_TYPE.LI_SHI_JIAN:{
                    cfg = localcache.getFilter(localdb.table_building_up,"building_type",t,"lv",Initializer.unionProxy.clubInfo.lsjLv)
                    this.lblBuildLv.string = i18n.t("UNION_BUILDING_2") + i18n.t("COMMON_LV",{lv:Initializer.unionProxy.clubInfo.lsjLv});
                }
                break;
                case CLUP_BUILD_TYPE.SHANG_FANG:{
                    cfg = localcache.getFilter(localdb.table_building_up,"building_type",t,"lv",Initializer.unionProxy.clubInfo.spLv)
                    this.lblBuildLv.string = i18n.t("UNION_BUILDING_5") + i18n.t("COMMON_LV",{lv:Initializer.unionProxy.clubInfo.spLv});
                }
                break;
                case CLUP_BUILD_TYPE.JIANYAN_TANG:{
                    cfg = localcache.getFilter(localdb.table_building_up,"building_type",t,"lv",Initializer.unionProxy.clubInfo.jytLv)
                    this.lblBuildLv.string = i18n.t("UNION_BUILDING_4") + i18n.t("COMMON_LV",{lv:Initializer.unionProxy.clubInfo.jytLv});;
                }
                break;  
            }
            if (cfg){
                this.lblDes.string = cfg.msg ? cfg.msg : " ";
                this.lblLevel.string = cfg.need;
                this.lblFund.string = cfg.cost ? cfg.cost[0].count : i18n.t("COMMON_LEVEL_MAX");
                let clubInfoData = Initializer.unionProxy.clubInfo;
                this.btn.interactable = clubInfoData.level >= cfg.need && cfg.cost && Initializer.bagProxy.getItemCount(cfg.cost[0].id) >= cfg.cost[0].count;
                this.btn.node.active = Initializer.unionProxy.memberInfo.post <= 2;
                this.icon.url = UIUtils.uiHelps.getXunfangIcon(cfg.icon);
            }
            
        }
    },
});
