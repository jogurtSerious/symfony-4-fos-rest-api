<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
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
 *      "Book", 
 *      pluralize=false
 * )
 */
class BookController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var BookRepository
     */
    private $bookRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookRepository $bookRepository
    ) {
        $this->entityManager    = $entityManager;
        $this->bookRepository  = $bookRepository;
    }

    /**
     * @param string $id
     * 
     * @return Book
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundException
     */
    private function findBookById(string $id){
        $existingBook = $this->bookRepository->find($id); 

        if (null === $existingBook){
            throw new NotFoundHttpException();
        }

        return $existingBook; 
    }

    public function getAction(string $id){
        return $this->view(
            $this->findBookById($id)
        );
    }

    public function cgetAction(){
        return $this->view(
            $this->bookRepository->findAll()
        );
    }

    public function postAction(
        Request $request
    ) {
        $form = $this->createForm(BookType::class, new Book());

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
        $existingBook = $this->findBookById($id);

        $form = $this->createForm(BookType::class, $existingBook);

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
        $exisitingBook = $this->findBookById($id);

        $form = $this->createForm(BookType::class, $exisitingBook); 

        $form->submit($request->request->all(), false);
        
        if(false === $form->isValid()){
            return $this->view($form);
        }

        $this->entityManager->flush(); 

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    public function deleteAction(string $id){
        $book = $this->findBookById($id); 

        $this->entityManager->remove($book); 
        $this->entityManager->flush(); 

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
