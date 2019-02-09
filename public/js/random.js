try {
    // for Node.js
    // var autobahn = require('autobahn');
} catch (e) {
    // for browsers (where AutobahnJS is available globally)
}

wampServer = $('#connection').data('connectionUrl');

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
    let rpc = 'com.thruwaydemo.random.description';
    session.call(rpc, []).then(
        function (res) {
            console.log(res, res.name);
            log(`RCP '${rpc}' returned "${res.name}"`);
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

    visitorNotification = 'com.thruwaydemo.visit';
    log("Subscribed to " + visitorNotification);
    session.subscribe(visitorNotification, function(args) {
        ip = args[0].ip;
        log(`Visitor from ${ip}`);
        // $('#thruway-log').prepend(`<li>${args[0]}</li>`);
        $('#last-visitor-ip').html(ip);
    });



    // 1) subscribe to a topic
    function onevent(args) {
        console.log("Event:", args[0]);
    }
    // session.subscribe('com.myapp.hello', onevent);

    subscription = 'com.thruwaydemo.randomsubpub';
    log("Subscribed to " + subscription);
    session.subscribe(subscription, function(args) {
        log(`Received ${args[0]} from ${subscription}`);
        // $('#thruway-log').prepend(`<li>${args[0]}</li>`);
        let $num = args[0];
        $('#random-number').html($num);
    });

    // 2) publish an event
    session.publish('com.myapp.hello', ['Hello, world!']);

    // 3) register a procedure for remoting
    function add2(args) {
        return args[0] + args[1];
    }
    // session.register('com.myapp.add2', add2);

};

log('opening autobahn connection  to ' + wampServer);

connection.open();

