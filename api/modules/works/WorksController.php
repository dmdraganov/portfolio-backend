<?php
require_once dirname(__DIR__, 2) . '/core/JsonRepository.php';
require_once dirname(__DIR__, 2) . '/core/Response.php';
require_once dirname(__DIR__, 2) . '/core/Request.php';

class WorksController
{
    private JsonRepository $labsRepository;
    private JsonRepository $practicesRepository;

    public function __construct()
    {
        $this->labsRepository = new JsonRepository('labs.json');
        $this->practicesRepository = new JsonRepository('practices.json');
    }

    public function getAll(Request $request, Response $response)
    {
        $labs = $this->labsRepository->findAll();
        $practices = $this->practicesRepository->findAll();

        // Add a 'type' field to distinguish them on the client-side
        foreach ($labs as &$lab) {
            $lab['work_type'] = 'lab';
        }
        foreach ($practices as &$practice) {
            $practice['work_type'] = 'practice';
        }

        $allWorks = array_merge($labs, $practices);
        
        $response->json($allWorks);
    }
}
