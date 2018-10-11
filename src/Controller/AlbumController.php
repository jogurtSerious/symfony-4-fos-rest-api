<?php

namespace App\Controller;


use App\Entity\Album;
use App\Form\AlbumType;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Rest\RouteResource(
 *     "Album",
 *     pluralize=false
 * )
 */
class AlbumController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AlbumRepository
     */
    private $albumRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        AlbumRepository $albumRepository
    ) {
        $this->entityManager    = $entityManager;
        $this->albumRepository  = $albumRepository;
    }

     /**
     * @param string $id
     *
     * @return Album
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function findAlbumById(string $id){
        $existingAlbum = $this->albumRepository->find($id);

        if (null === $existingAlbum) {
            throw new NotFoundHttpException();
        }

        return $existingAlbum;
    }

    public function getAction(string $id){
        //date_default_timezone_set('Australia/Sydney');
        return $this->view(
            $this->findAlbumById($id)
        );
    }

    public function cgetAction(){
        return $this->view(
            $this->albumRepository->findAll()
        );
    }

    public function postAction(
        Request $request
    ) {
        $form = $this->createForm(AlbumType::class, new Album());

        $form->submit($request->request->all());

        if (false === $form->isValid()) {

            return $this->handleView(
                $this->view($form)
            );
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return $this->handleView(
            $this->view(
                [
                    'status' => 'ok',
                ],
                Response::HTTP_CREATED
            )
        );
    }

    public function putAction(
        Request $request, string $id
    ){
        $existingAlbum = $this->findAlbumById($id);

        $form = $this->createForm(AlbumType::class, $existingAlbum);

        $form->submit($request->request->all());

        if (false === $form->isValid()) {
            return $this->view($form);
        }

        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    public function patchAction(
        Request $request, string $id
    ){
        $exisitingAlbum = $this->findAlbumById($id);

        $form = $this->createForm(AlbumType::class, $exisitingAlbum); 

        $form->submit($request->request->all(), false);
        
        if(false === $form->isValid()){
            return $this->view($form);
        }

        $this->entityManager->flush(); 

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    public function deleteAction(string $id){
        $album = $this->findAlbumById($id); 

        $this->entityManager->remove($album); 
        $this->entityManager->flush(); 

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

}
