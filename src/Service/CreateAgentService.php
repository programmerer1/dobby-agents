<?php

namespace App\Service;

use App\Dto\CreateAgentDto;
use App\Entity\Agent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CreateAgentService
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager,
        public readonly ValidatorInterface $validator,
        public readonly Agent $agent,
        public readonly UrlGeneratorInterface $urlGeneratorInterface,
        public readonly UserService $userService
    ) {}

    public function createAgent(CreateAgentDto $createAgentDto): JsonResponse
    {
        $this->agent->setUsername(strtolower($createAgentDto->username))
            ->setName($createAgentDto->name)
            ->setDescr($createAgentDto->descr)
            ->setSystemPrompt($createAgentDto->systemPrompt)
            ->setIsPublic((bool) $createAgentDto->isPublic)
            ->setLogo(null)
            ->setMaxTokens($createAgentDto->maxTokens)
            ->setTemperature($createAgentDto->temperature)
            ->setTopK($createAgentDto->topK)
            ->setTopP($createAgentDto->topP)
            ->setFrequencyPenalty($createAgentDto->frequencyPenalty)
            ->setPresencePenalty($createAgentDto->presencePenalty)
            ->setUser($this->userService->getUser());

        $errors = $this->validator->validate($this->agent);
        $response = [];

        if (count($errors) > 0) {
            $response['status'] = 422;

            foreach ($errors as $error) {
                $response['errors'][] = [
                    'path' => $error->getPropertyPath(),
                    'message' =>  $error->getMessage()
                ];
            }
        } else {
            $this->entityManager->persist($this->agent);
            $this->entityManager->flush();
            $response = [
                'status' => 200,
                'message' => 'Your agent has been created',
                'redirect' => $this->urlGeneratorInterface->generate('app_dashboard_my_agents')
            ];
        }

        return new JsonResponse($response);
    }
}
