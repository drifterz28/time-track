'use strict';
var React = require('react');
var Actions = require('../js/lib/actions');
var Store = require('../js/lib/stores');

var moment = require('moment');

module.exports = React.createClass({
    getInitialState:function() {
        var times = Store.getTimes();
        return {
            times: times
        };
    },
    componentDidMount: function() {
        this.unsubscribe = Store.listen(this.onChange);
    },
    componentWillUnmount: function() {
        this.unsubscribe();
    },
    onChange: function() {
        var times = Store.getTimes();
        this.setState({times: times});
    },
    TrackTime: function() {
        Store.trackTime(this.state.timeState);
        var state = (this.state.timeState === 'In') ? 'Out' : 'In';
        this.setState({
            timeState: state
        });
    },
    render: function() {
        var data = this.state.times;
        var totalTime = 0;
        var times = data.map(function(data, i) {
            var outTime = '';
            var inTime = moment(data.In).format("MM/DD/YYYY hh:mm a");
            if(data.Out) {
                var b = moment(data.In);
                var a = moment(data.Out);
                var timeDiff = +Math.max( Math.round( a.diff(b, 'hours', true) * 10) / 10, 2.8 ).toFixed(1);
                totalTime = totalTime + timeDiff;
                outTime = moment(data.Out).format("MM/DD/YYYY hh:mm a");
            }
            return (
                <li className="list-group-item" key={i}>
                    {data.Out ? <span className="badge">{timeDiff}<br/> hours</span> : ''}
                    In: {inTime} <br/> Out: {outTime}
                </li>
            );
        });
        return (
            <div className="time-list">
                <h4>Time Card</h4>
                <ul className="list-group">
                    {times}
                </ul>
                <h4 className="total-time">Total Hours: {totalTime}</h4>
            </div>
        );
    }
});
