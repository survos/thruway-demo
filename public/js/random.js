try {
    // for Node.js
    // var autobahn = require('autobahn');
} catch (e) {
    // for browsers (where AutobahnJS is available globally)
}

wampServer = $('#connection').data('connectionUrl');
console.log('connecting to ' + wampServer);

$('#xthruway-log li:first-child').remove();


var connection = new autobahn.Connection({url: wampServer, realm: 'realm1'});

function log(s) {


    $logList = $('#thruway-log');
    $logList.append(`<li>${s}</li>`);

    logLength = $logList.children().length; // $('#thruway-log li').length;

    if ( logLength > 9 ) {
        $('#thruway-log li:first-child').remove();
    }
}

connection.onopen = function (session) {
    log('autobahn connection successful!');

    // 4) call a remote procedure
    session.call('com.thruwaydemo.random.description', []).then(
        function (res) {
            console.log(res);
            $('#random-number').html(res);
            log("Result:", res);
        },
        function (error) {
            console.log("Error:", res);
        },

    );


    // 4) call a remote procedure
    session.call('com.thruwaydemo.random', [2, 10]).then(
        function (res) {
            $('#random-number').html(res);
            log("Result:", res);
        },
        function (error) {
            console.log("Error:", res);
        },

    );

    // 1) subscribe to a topic
    function onevent(args) {
        console.log("Event:", args[0]);
    }
    session.subscribe('com.myapp.hello', onevent);

    subscription = 'com.thruwaydemo.randomsubpub';
    session.subscribe(subscription, function(args) {
        log(`Received ${args[0]} from ${subscription}`);
        // $('#thruway-log').prepend(`<li>${args[0]}</li>`);
        console.log(args);
    });

    // 2) publish an event
    session.publish('com.myapp.hello', ['Hello, world!']);

    // 3) register a procedure for remoting
    function add2(args) {
        return args[0] + args[1];
    }
    // session.register('com.myapp.add2', add2);

};

console.log('opening autobahn connection ...');
connection.open();

