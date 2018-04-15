<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        $users = $this->get('doctrine.orm.entity_manager')->getRepository('App:User')->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/user/new", name="create_user")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUserAction(Request $request)
    {
        $form = $this->createForm(UserFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('user/form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/user/{id}/edit", name="edit_user")
     *
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editUserAction(Request $request, User $user)
    {
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->get('doctrine.orm.entity_manager');
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('user/form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/user/{id}/send-notification", name="send_notification")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function sendNotification(User $user)
    {
        $manager = $this->get('mgilet.notification');
        $notif = $manager->createNotification('Hello world !');
        $notif->setMessage('This a notification.');
        $notif->setLink('http://symfony.com/');
        // or the one-line method :
        // $manager->createNotification('Notification subject','Some random text','http://google.fr');

        // you can add a notification to a list of entities
        // the third parameter ``$flush`` allows you to directly flush the entities
        $manager->addNotification([$user], $notif, true);

        return $this->redirectToRoute('home');
    }

}