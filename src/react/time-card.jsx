'use strict';
var React = require('react');
var Actions = require('../js/lib/actions');
var Store = require('../js/lib/stores');

var Times = require('./times.jsx');

module.exports = React.createClass({
    getInitialState:function() {
        return {
            name: Store.getName(),
            timeState: Store.getTimeState()
        };
    },
    componentDidMount: function() {
        this.unsubscribe = Store.listen(this.onChange);
    },
    componentWillUnmount: function() {
        this.unsubscribe();
    },
    onChange: function() {
        console.log('change');
        //this.setState({needsSignin: Store.isSignedIn()});
    },
    TrackTime: function() {
        Store.trackTime(this.state.timeState);
        var state = (this.state.timeState === 'In') ? 'Out' : 'In';
        this.setState({
            timeState: state
        });
    },
    render: function() {
        return (
            <div className="time-card">
                <h3>Welcome {this.state.name}</h3>
                <button onClick={this.TrackTime} className="btn btn-success btn-lg btn-block">Clock {this.state.timeState}</button>
                <Times/>
            </div>
        );
    }
});
