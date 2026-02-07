<?php

namespace App\Infrastructure\Security\Controller;

use App\Infrastructure\Security\Command\LoginCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class LoginController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';

        try {
            $token = $this->handle(LoginCommand::create((string) $email));

            return new JsonResponse([
                'token' => $token,
                'email' => $email
            ], Response::HTTP_OK);

        } catch (Throwable $e) {
            return new JsonResponse([
                'error' => 'Nieprawidłowe dane logowania.'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
