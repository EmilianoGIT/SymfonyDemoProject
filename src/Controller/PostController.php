<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Services\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//here a have my prefix for the route

    /**
     * @Route("/post", name="post.")
     */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PostRepository $postRepository)
    {
        $posts = $postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }


    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request, FileUploader $fileUploader)
    {
        $post = new Post();
        //$post->setTitle('This is a new post');

        //I need to pass the form I want to use (when using ::class I can pass the full qualified namespace for that class)
        //and the 2nd is the object that is going to be bounded with (or just null if you don't have)
        $form = $this->createForm(PostType::class, $post);


        $form->handleRequest($request);
        $form->getErrors();

        //$form->isValid()
        if($form->isSubmitted() && $form->isValid()){

            //entity manager
            $em = $this->getDoctrine()->getManager();

            $file = $request->files->get('post')['image'];

            //if I actually have I file from the client
            if($file){

                $filename = $fileUploader->uploadFile($file);

                $post->setImage($filename);

                $em->persist($post);
                //this send all of the query that were constructed with persist function and execute all together 
                $em->flush();

            }
            
            return $this->redirect($this->generateUrl('post.index'));

        }

        return $this->render('post/create.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    //OLD SHOW METHOD, WHICH HAS id AND postRepository AS PARAMETERS
    // /**
    //  * @Route("/show/{id}", name="show")
    //  * @return Response
    //  */
    // public function show($id, PostRepository $postRepository)
    // {
    //     $post = $postRepository->find($id);


    //     dump($post);
    //     die;
    //     return $this->render('post/show.html.twig',[
    //         'post' => $post
    //     ]);
    // }


    /**
     * @Route("/show/{id}", name="show")
     * @param Post $post
     * @return Response
     * 
     */
    public function show($id, PostRepository $postRepository)
    {
        $post = $postRepository->findPostWithCategory($id);
        //$post = $postRepository->find($id);
        //dump($post);
        return $this->render('post/show.html.twig',[
            'post' => $post
        ]);
    }





    /**
     * @Route("/delete/{id}", name="delete")
     * @param Post $post
     * @return Response
     * 
     */
    public function remove(Post $post, FileUploader $fileUploader)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $fileUploader->deleteFile($post->getImage());
        $em->flush();

        $this->addFlash('success','Post was removed');

        return $this->redirect($this->generateUrl('post.index'));
    }


}
