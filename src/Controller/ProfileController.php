<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(ProfileRepository $profileRepository, UserRepository $userRepository)
    {
        $user = $this->getUser();
        $userid = $user->getId();

        $profile = $profileRepository->findUserProfileByUserId($userid);
        //if I don't have the profile record related to this user then make it
        if(empty($profile)){
            $profile = new Profile;
            $profile->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($profile);
            $em->flush();
        }

        $profile = $profileRepository->findUserProfileByUserId($userid);
        return $this->render('profile/index.html.twig', [
            'profile' => $profile
        ]);

    }
}
