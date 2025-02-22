/**
  * Created by taozh on 2017/6/22.
  * taozh1982@gmail.com
  */
// var EventEmitter = function () {
//  this.__z_e_listeners = {};
// };

module.exports = {
    __z_e_listeners: {},

    on: function (evt, handler, context) {
        var handlers = this.__z_e_listeners[evt];
        if (handlers === undefined) {
            handlers = [];
            this.__z_e_listeners[evt] = handlers;
        }
        var item = {
            handler: handler,
            context: context
        };
        handlers.push(item);
        return item;
    },

    off: function (evt, handler, context) {
        var handlers = this.__z_e_listeners[evt];
        if (handlers !== undefined) {
            var size = handlers.length;
            for (var i = 0; i < size; i++) {
                var item = handlers[i];
                if (item.handler === handler && item.context === context) {
                    handlers.splice(i, 1);
                    return;
                }
            }
        }
    },

    emit: function (type, event) {
        var hanlders = this.__z_e_listeners[type];
        if (hanlders !== undefined) {
            var size = hanlders.length;
            for (var i = 0; i < size; i++) {
                var ef = hanlders[i];
                var handler = ef.handler;
                var context = ef.context;
                handler.apply(context, [event]);
            }
        }
    },
};
// EventEmitter.prototype.
// EventEmitter.prototype.
// EventEmitter.prototype.