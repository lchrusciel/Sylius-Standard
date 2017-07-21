<?php

declare(strict_types=1);

namespace AppBundle\Controller\Checkout;

use AppBundle\Command\AuthorizeAdyenPayment;
use AppBundle\Command\CaptureAdyenPayment;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizeAdyenPaymentAction extends Controller
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var CommandBus */
    private $bus;

    public function __construct(ViewHandlerInterface $viewHandler, CommandBus $bus)
    {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
    }

    public function __invoke(Request $request): Response
    {
        $this->bus->handle(new AuthorizeAdyenPayment(
            $request->attributes->get('token'),
            $request->attributes->get('paymentId'),
            $request->request->get('creditCard')
        ));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }
}
