# thruway-demo
Demonstration of Symfony4 and Thruway (RPC, Sub/Pub).

See this demonstration in action at https://thruway-demo.herokuapp.com/

The purpose of this application is to demonstrate how a web page can by dynamically updated from a background server process, such as processing a queue or running a command line script.  The demonstration will simply publish random numbers, but a real-life application might be monitoring sending a newsletter or downloading files.

The app consists of a single web page, which first makes a RPC (Remote Procedure Call) that gets some basic information for display.  It then subscribes to a topic that's published from a command line script and from a controller.

## Running locally

    git@github.com:survos/thruway-demo.git
    cd thruway-demo
    composer install
    
    # start up the server
    bin/console server:start
    
    # start up the thruway router and worker
    bin/console thruway:process start &
    
    OR if you want to watch the calls and not have it run in the background:

    bin/console thruway:process start -vvv

    # now open a web browser http://127.0.0.1:8000/
    
The web page is now subscribed to topic to receive and display a random number, and also to listen for visitors to the /visit page.  Start the random number publisher:

    bin/console app:publish-random-numbers
    
The web page will start displaying the random numbers published by the command line script.  When you open the visitor page, the controller publishes a notification too, simply by injecting the service and publishing.

    // AppController.php
    /**
     * @Route("/visit", name="visit")
     */
    public function visit(Request $request, ClientManager $thruway)
    {
        // publish to anyone who's subscribed
        $ip = $request->getClientIp();
        $thruway->publish('com.thruwaydemo.visit', [ ['ip' => $ip ] ]);
        
    
## Running on heroku

A summary of the commands at https://devcenter.heroku.com/articles/getting-started-with-symfony#deploying-to-heroku

Well, this is supposed to work.  

    heroku create
    heroku config:set APP_ENV=prod
    
    # WAMP_SERVER is an environment variables used in voryx.yaml. 
    heroku config:set WAMP_SERVER=ws://<yourAppName>.herokuapp.com:8081
    git push heroku master
    
    # the web server automatically starts.  Manually start the thruway worker, which is defined in Procfile
    heroku ps:scale thruway=1

    # open the web page
    heroku open
    
    # monitor it
    heroku logs --tail -d worker
    
    

## Noteworthy (Symfony4 and Flex)

Wire the ClientManager so that it can be injected into the controller and services

    # services.yaml
    Voryx\ThruwayBundle\Client\ClientManager:
        alias: thruway.client
    
    
Now simply pass in the service in the constructor or controller:

    use Voryx\ThruwayBundle\Client\ClientManager;
    
    ...

    private $thruwayClient;
    public function __construct(ClientManager $thruwayClient, $name = null)
    {
        parent::__construct($name);
        $this->thruwayClient = $thruwayClient;
    }
    
    
Because Heroku only allows one free dyno besides the web server, and we're using the free dyno for the thruway processes, we can't run the random number publisher without paying for it.  But we can still monitor the visitor page.


    
    
    
    
    
