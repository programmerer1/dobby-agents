<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
use App\Dto\{CreateAgentDto, UpdateAgentDto};
use App\Entity\Agent;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Service\{CreateAgentService, PaginateAgentService, UpdateAgentService};

final class AgentController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/dashboard.html.twig');
    }

    #[Route('/dashboard/my-agents', name: 'app_dashboard_my_agents')]
    public function myAgents(): Response
    {
        return $this->render('dashboard/agents.html.twig');
    }

    #[Route('/dashboard/agents/load', name: 'app_dashboard_agents_load', methods: ['GET'], format: 'json')]
    public function loadAgents(PaginateAgentService $paginateAgentService): JsonResponse
    {
        return $paginateAgentService->getAgents($this->getUser());
    }

    #[Route('/dashboard/my-agents/load', name: 'app_dashboard_my_agents_load', methods: ['GET'], format: 'json')]
    public function loadMyAgents(PaginateAgentService $paginateAgentService): JsonResponse
    {
        return $paginateAgentService->getMyAgents($this->getUser());
    }

    #[Route('/dashboard/create-agent', name: 'app_dashboard_create_agent', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('dashboard/create-agent.html.twig', [
            'controller_name' => 'Dashboard/AgentController',
        ]);
    }

    #[Route('/dashboard/create-agent', name: 'app_dashboard_create_agent_store', methods: ['POST'], format: 'json')]
    public function store(
        #[MapRequestPayload()] CreateAgentDto $createAgentDto,
        CreateAgentService $createAgentService
    ): Response {
        return $createAgentService->createAgent($createAgentDto, $this->getUser());
    }

    #[Route('/dashboard/agent/{id}/edit', name: 'app_dashboard_edit_agent', methods: ['GET'])]
    public function edit(Agent $agent): Response
    {
        if ($this->isGranted('agent_view', $agent) == false) {
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/update-agent.html.twig', [
            'agent' => $agent,
        ]);
    }

    #[Route('/dashboard/agent/{id}/edit', name: 'app_dashboard_edit_agent_store', methods: ['POST'], format: 'json')]
    public function update(
        Agent $agent,
        #[MapRequestPayload()] UpdateAgentDto $updateAgentDto,
        UpdateAgentService $updateAgentService
    ): JsonResponse {
        if ($this->isGranted('agent_edit', $agent) == false) {
            return $this->json(['status' => 403, 'error' => 'Access denied']);
        }

        return $updateAgentService->updateAgent($updateAgentDto, $agent);
    }
}
