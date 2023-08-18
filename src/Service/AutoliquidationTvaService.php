<?php

namespace App\Service;



use Symfony\Component\HttpFoundation\Response;


class AutoliquidationTvaService extends \DateTime
{
    public function calculTva(): Response | string
    {
        return 'hello';

    }
}