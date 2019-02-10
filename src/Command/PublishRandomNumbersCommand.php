<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Voryx\ThruwayBundle\Annotation\Register;
use Voryx\ThruwayBundle\Client\ClientManager;

class PublishRandomNumbersCommand extends Command
{

    protected static $defaultName = 'app:publish-random-numbers';

    private $thruwayClient;
    public function __construct(ClientManager $thruwayClient, $name = null)
    {
        parent::__construct($name);
        $this->thruwayClient = $thruwayClient;
    }

    protected function configure()
    {
        $this
            ->setDescription('Long-running process that publishes random numbers to subscribers')
            ->addArgument('topic', InputArgument::OPTIONAL, 'Topic Code to published', 'com.thruwaydemo.random' )
            ->addOption('sleep', null, InputOption::VALUE_OPTIONAL, 'seconds to sleep between calls', 2)
        ;
    }

    /**
     * @Register("com.thruwaydemo.random.description")
     */
    public function randomDescription()
    {
        return [
            'name' => "Random Number Publisher",
        ];
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);

        $topic = $input->getArgument('topic');
        $sleepTime = $input->getOption('sleep');

        $io->note("Connecting to " . getenv('WAMP_SERVER'));

        $infoTopic  = 'com.thruwaydemo.random.description';
        $this->thruwayClient->call($infoTopic, [])->then(
            function ($res) use ($io) {
                // dump($res, $res[0]);
                $info = $res[0];
                dump('info', $info, 'name', $info->name);
                print $info->name;
                // print "Received " + (string)$info->name;
                // $io->success("Received " + (string)$info->name);
                // publish the random number
            }
        )->done(function ($res) {
            // die("Done");
            // dump($res);
        });


        $io->note(sprintf('Publishing Random Numbers to %s', $topic));

        // make an RPC call, just to show how to do it.

        while (true) {
            $randomNumber = rand(0, 2^8);
            $io->note(sprintf('calling %s with %d ...', $topic, $randomNumber));
                $this->thruwayClient->publish('com.thruwaydemo.randomsubpub', [$randomNumber]);


                /* too tricky, call an RPC !
                $this->thruwayClient->call($topic, [0, 200])->then(
                    function ($res) {
                        // publish the random number
                        echo $res[0];
                    }
                )->done(function ($res) {
                    dump($res);
                });
                */
            try {
            } catch (\Exception $e) {
                $io->error($e->getCode());
                break;
            }
            $io->note("sleeping $sleepTime seconds...");
            sleep($sleepTime);
        }

        $io->success('Stopped publishing random numbers.');
    }
}
