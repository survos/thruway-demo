<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Voryx\ThruwayBundle\Annotation\Register;

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
