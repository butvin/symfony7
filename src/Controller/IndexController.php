<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Index;
use App\Repository\IndexRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly IndexRepository $repository,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route('/index/index', name: 'app_index_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        return $this->json(phpinfo());
    }

    #[Route('/index/{id}', name: 'app_index_update', methods: ['GET', 'POST'])]
    public function update(string $uuid, Request $request): JsonResponse
    {
        $index = $this->repository->findOneBy(['uuid' => $uuid]);
        $data = json_decode($request->getContent(), true);

        if ($index->getDeletedAt() !== null) {
            return $this->json(["success" => false, "message" => "not found", "data" => null], Response::HTTP_NOT_FOUND);
        }

        // Get one entity by uuid
        // Amplify data to this entity
        // Store undated entity with new data to DB + add datetime to updated_at

        return $this->json(["success" => true , "message" => "ok", "data" => $data]);
    }

    #[Route('/index/{id}', name: 'app_index_delete', methods: ['DELETE'])]
    public function delete(string $uuid, Request $request): JsonResponse
    {
        $index = $this->repository->findOneBy(['uuid', $uuid]);

        if (!$index instanceof Index || $index->getDeletedAt() !== null) {
            return $this->json("not found by uuid" . $uuid, Response::HTTP_NOT_FOUND);
        }

        return $this->json($index->getHash(), Response::HTTP_NO_CONTENT);
    }

    /**
     * @throws \Random\RandomException;
     * @throws \Exception;
     */
    #[Route('/index/add',
        name: 'app_index_create',
        methods: ['POST'],
    )]
    public function create(Request $request): JsonResponse
    {
        $name = $request->request->get('name') ;

        if (null === $name) {
            $name = md5(base64_decode(random_bytes(4)));
        }

        $index = (new Index())
            ->setName($name)
            ->setHash(sha1(md5($name)))
            ->setActive(true)
            ->setCreatedAt(new \DateTimeImmutable())
        ;

        // todo: validate entity
        $this->em->persist($index);
        // todo: dispatch event
        $this->em->flush();

        return new JsonResponse(
            [
                'success' => true,
                'message' => sprintf("Hash '%s' saved with uuid: '%s'", $index->getHash(), $index->getUuid()),
                'data' => [
                    'uuid' => $index->getUuid(),
                    'name' => $index->getName(),
                    'active' => $index->getActive(),
                    'hash' => $index->getHash(),
                    'createdAt' => $index->getCreatedAt()?->format('Y-m-d H:i:s'),
                ],
            ],
            Response::HTTP_OK,
            [
                'Access-Control-Allow-Origin' => '*',
            ]
        );
    }

    #[Route('/index/list', name: 'app_index_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $list = $this->repository->findAllAsArray();

        if (count($list) == 0) {
            return $this->json('empty set', Response::HTTP_NOT_FOUND);
        }

        return $this->json($list, Response::HTTP_OK);
    }
}
