<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SSH\MsJwtBundle\Annotations\Mapping;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 *
 * @Route("/front")
 * @Security("is_granted('ROLE_FRONT')")
 */
class AcompteController extends AbstractController
{

    /**
     *
     * @var \App\Manager\AcompteManager
     */
    private $manager;

    public function __construct(\App\Manager\AcompteManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/acomptes", name="front-acomptes", methods={"GET"})
     * @Mapping(object="App\ApiModel\Acompte\Acomptes", as="Acomptes")
     *
     */
    public function Acomptes(): Array
    {
        return $this->manager
            ->init()
            ->acomptes();
    }

    /**
     * @Route("/acomptes/choices", name="front-list-acomptes", methods={"GET"})
     */
    public function AcomptesChoice(): Array
    {
        return $this->manager
            ->init()
            ->acomptesChoice();
    }

    /**
     * @Route("/acompte", name="front-create-acompte", methods={"POST"})
     * @Mapping(object="App\ApiModel\Acompte\Acompte", as="Acompte")
     */
    public function create(): Array
    {
        return $this->manager
            ->init()
            ->set();
    }

    /**
     * @Route("/acompte/{code}", name="front-get-acompte", methods={"GET"})
     */
    public function getOne($code): Array
    {
        return ['data' => $this->manager
            ->init(['code' => $code])
            ->getAcompte(true)
        ];
    }

    /**
     * @Route("/acompte/{code}", name="front-set-acompte", methods={"PUT"})
     * @Mapping(object="App\ApiModel\Acompte\Acompte", as="Acompte")
     */
    public function set($code): Array
    {
        return $this->manager
            ->init(['code' => $code])
            ->set();
    }

}
