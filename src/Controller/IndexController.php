<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Index;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\IndexRepository;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly IndexRepository $repository,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route('/index/{id}', name: 'app_index_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $index = $this->repository->find($id);

        return $this->json(
            [
                'hash' => $index ? $index->getHash() : null,
            ],
        );
    }

    #[Route('/index/index', name: 'app_index_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        return $this->json(
            $this->repository->findAllAsArray()
        );
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    #[Route('/index/add', name: 'app_index_add', methods: ['POST', 'GET'])]
    public function add(Request $request): JsonResponse
    {
        $name = $request->request->get('name') ;
        //$hash = $request->request->get('hash');

        $index = new Index();
        $index->setName(
            $name ?? 'noname' . "_" . md5(base64_decode(random_bytes(8)))
        );
        $index->setHash(md5(base64_decode(random_bytes(32))));
        $index->setActive(true);
        $index->setCreatedAt(new \DateTimeImmutable(
            'now',
            new \DateTimeZone('Europe/Kiev')
        ));
        $index->setUpdatedAt(new \DateTimeImmutable(
            'now',
            new \DateTimeZone('Europe/Berlin')
        ));

        $this->em->persist($index);
        $this->em->flush();

        return new JsonResponse(
            [
                'message' => sprintf('Saved successfully with id: %s',  $index->getId()),
                'hash' => $index->getHash(),
            ],
            Response::HTTP_OK,
            [
                'Access-Control-Allow-Origin' => '*',
            ]
        );
    }
}
