// basic store, not compleate
'use strict';
var Reflux = require('reflux');
var Actions = require('./actions');
var moment = require('moment');

var _info = (localStorage.timeTrack) ? JSON.parse(localStorage.timeTrack) : [];

module.exports = Reflux.createStore({
    init: function() {
        this.listenTo(Actions.ClockIn, this.onCreate);
        this.listenTo(Actions.ClockOut, this.onEdit);
    },
    // called on save
    onCreate: function(info) {
    },
    // called on edit save
    onEdit: function(info) {
    },
    getName: function() {
        return localStorage.timeTracker;
    },
    signIn: function(name, pin) {
        console.log(name + ' - ' + pin);
        localStorage.timeTracker = name;
        this.trigger();
    },
    isSignedIn: function() {
        if(localStorage.timeTracker) {
            return true;
        } else {
            return false;
        }

    },
    // called on load from jsx template
    getTimes: function() {
        return _info;
    },
    getTimeState: function() {
        if(_info[0].In && !_info[0].Out) {
            return 'Out';
        } else {
            return 'In';
        }
    },
    trackTime: function(state) {
        var clockTime = moment().format('YYYY/MM/DD HH:mm');
        var info = {};
        if(_info[0] && _info[0][state] || _info.length < 1) {
            info[state] = clockTime;
            _info.unshift(info);
        } else {
            _info[0][state] = clockTime;
        }

        this.save();
        this.trigger();
    },
    save: function() {
        localStorage.timeTrack = JSON.stringify(_info);
    }
});
