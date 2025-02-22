var GuideProxy = function() {
    
    this.ctor = function() {
        // change new guide --2020.08.11
        //this.UPDATE_TRIGGER_GUIDE = "UPDATE_TRIGGER_GUIDE";
        //this.UPDATE_TRIGGER = "UPDATE_TRIGGER";
        this.guideUI = null;
    }

    this.sendGuide = function(t) {
        var e = new proto_cs.guide.guide();
        e.gnew = t;
        JsonHttp.send(e);
    };
    this.sendGuideUpGuan = function() {
        JsonHttp.send(new proto_cs.guide.guideUpguan());
    };

}
exports.GuideProxy = GuideProxy;
