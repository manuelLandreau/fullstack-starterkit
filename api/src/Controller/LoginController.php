<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoginController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/login", name="user_login")
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return JsonResponse
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $vars = json_decode($request->getContent(), true);
        $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');

        $user = $this->userRepository->loadUserByUsername($vars['username']);

        if (!is_null($user) && $encoder->isPasswordValid($user, $vars['plainPassword'])) {
            return $this->json([
                'user' => $user,
                'jwt' => $jwtManager->create($user),
            ], 200);
        }

        return $this->json([
            'error' => empty($user) ? 'User not found' : 'Password doesn\'t match',
        ], 401);
    }

    /**
     * @Route("/register", name="user_registration")
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ValidatorInterface           $validator
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
    {
        $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');
        $vars = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($vars['email']);
        $user->setUsername($vars['username']);
        $user->setPlainPassword($vars['plainPassword']);

        $errors = $validator->validate($user);

        if (isset($errors)) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            return $this->json($errors, 400);
        }

        // 3) Encode the password (you could also do this via Doctrine listener)
        $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);

        // 4) save the User!
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // ... do any other work - like sending them an email, etc
        // maybe set a "flash" success message for the user

        return $this->json([
            'user' => $user,
            'jwt' => $jwtManager->create($user),
        ], 201);
    }
}
