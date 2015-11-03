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
    // called on note click to edit
    getInfoById: function(id) {
        for(var i = 0; i < _info.length; i++) {
            if(_info[i]._id === id) {
                return _info[i];
            }
        }
    },
    save: function() {
        localStorage.timeTrack = JSON.stringify(_info);
    }
});
