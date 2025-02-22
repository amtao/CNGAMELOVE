cc.Class({
    extends: cc.Component,
    properties: {
        lblCount: cc.Label,
        slider: cc.Slider,
        progress: cc.ProgressBar,
        left: cc.Button,
        right: cc.Button,
        btnMax: cc.Button,
        showMmin:{
            set: function(t) {
                this._showMin = t;
                this.updateBtn();
            },
            enumerable: !0,
            configurable: !0
        },
        max:{
            get: function() {
                return this._max;
            },
            set: function(t) {
                this._max = t;
                this._max = this._max > 99 ? 99 : this._max;
                this.updateCurValue();
            },
            enumerable: !0,
            configurable: !0
        },
        curValue:{
            get: function() {
                return this._curValue + 1;
            },
            set: function(t) {
                this._curValue = t > this._max - 1 ? this._max - 1 : t;
                this.updateCurValue();
            },
            enumerable: !0,
            configurable: !0
        },
        curMax:{
            get: function() {
                return this._max - 1;
            },
            enumerable: !0,
            configurable: !0
        },
    },
    ctor() {
        this.changeHandler = null;
        this._max = 100;
        this._curValue = 0;
        this._showMin = 0;
    },
    updateCurValue() {
        this.slider.progress = this.max <= 1 ? 0 : this._curValue / this.curMax;
        this.progress.progress = this.slider.progress;
        this.updateBtn();
    },
    onLoad() {
        facade.send("CLOSE_SEND_MOVE", !1);
        if (0 == this._curValue) {
            this.slider.progress = 0;
            this.progress.progress = 0;
            this.onSildeEvent();
        }
        this.scheduleOnce(this.onSildeEvent, 0.1);
    },
    onClickAdd(t, e) {
        var o = parseInt(e);
        this._curValue += o;
        this._curValue = this._curValue < 0 ? 0 : this._curValue;
        this._curValue = this._curValue > this.curMax ? this.curMax: this._curValue;
        this.progress.progress = this.slider.progress = this.max <= 1 ? 0 : this._curValue / this.curMax;
        this.updateBtn();
        this.updateMaxBtn();
    },
    updateMaxBtn() {
        this.btnMax && (this.btnMax.interactable = this.curValue < this.max);
    },
    onClickMax(t, e) {
        this._curValue = this.curMax;
        this.progress.progress = this.slider.progress = this.max <= 1 ? 0 : this._curValue / this.curMax;
        this.updateBtn();
        this.updateMaxBtn();
    },
    updateBtn() {
        this.lblCount && (this.lblCount.string = i18n.t("COMMON_COUNT", {
            c: i18n.t("COMMON_NUM", {
                f: this._showMin + this.curValue,
                s: this._showMin + parseInt(this.max + "")
            })
        }));
        this.left && (this.left.interactable = this.curValue > 1);
        this.right && (this.right.interactable = this.curValue < this.max);
    },
    onSildeEvent() {
        this.slider.progress = this.slider.progress > 1 ? 1 : this.slider.progress;
        this.progress.progress = this.slider.progress;
        this._curValue = Math.floor(this.slider.progress * this.curMax);
        this.updateBtn();
        this.updateMaxBtn();
        this.changeHandler && this.changeHandler();
    },
    onDestroy() {
        facade.send("CLOSE_SEND_MOVE", !0);
        this.unscheduleAllCallbacks();
    },
});
