<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Voryx\ThruwayBundle\Annotation\Register;
use Voryx\ThruwayBundle\Client\ClientManager;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app")
     */
    public function index()
    {
        return $this->render('app/index.html.twig', [
            'connectionUrl' => getenv('WAMP_SERVER')
        ]);
    }

    /**
     * @Route("/visit", name="visit")
     */
    public function visit(Request $request, ClientManager $thruway)
    {
        // publish to anyone who's subscribed
        $ip = $request->getClientIp();
        dump($ip);
        $thruway->publish('com.thruwaydemo.visit', [ ['ip' => $ip ] ]);

        return $this->render('app/visit.html.twig', [
            // 'connectionUrl' => getenv('WAMP_SERVER')
        ]);
    }


    /**
     * @Register("com.thruwaydemo.double")
     */
    public function doubleAction($number): int
    {
        return 2 * $number;
    }

    /**
     * @Register("com.thruwaydemo.random")
     */
    public function random($min=0, $max=200000): int
    {
        return random_int($min, $max);
    }



}
