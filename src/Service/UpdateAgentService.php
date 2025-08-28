<?php

namespace App\Service;

use App\Dto\UpdateAgentDto;
use App\Entity\Agent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UpdateAgentService
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager,
        public readonly ValidatorInterface $validator
    ) {}

    public function updateAgent(UpdateAgentDto $updateAgentDto, Agent $agent): JsonResponse
    {
        $agent->setName($updateAgentDto->name)
            ->setDescr($updateAgentDto->descr)
            ->setSystemPrompt($updateAgentDto->systemPrompt)
            ->setIsPublic((bool) $updateAgentDto->isPublic)
            ->setMaxTokens($updateAgentDto->maxTokens)
            ->setTemperature($updateAgentDto->temperature)
            ->setTopK($updateAgentDto->topK)
            ->setTopP($updateAgentDto->topP)
            ->setFrequencyPenalty($updateAgentDto->frequencyPenalty)
            ->setPresencePenalty($updateAgentDto->presencePenalty);

        $errors = $this->validator->validate($agent);
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
            $this->entityManager->flush();
            $response = ['status' => 200, 'message' => 'Data saved'];
        }

        return new JsonResponse($response);
    }
}
