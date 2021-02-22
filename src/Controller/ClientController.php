<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ClientController extends AbstractController
{
    /**
     * @Route("/api/users", name="users", methods={"GET"})
     */
    public function getUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->json($users, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/users/{id}", name="user", methods={"GET"})
     */
    public function getUserById(UserRepository $userRepository, $id): Response
    {
        $users = $userRepository->find($id);
        return $this->json($users, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/clients", name="clients", methods={"GET"})
     */
    public function getClients(ClientRepository $clientRepository): Response
    {
        $clients = $clientRepository->findAll();
        return $this->json($clients, 200, [], ['groups' => 'client:read']);
    }

    /**
     * @Route("/api/clients/{id}", name="client", methods={"GET"})
     */
    public function getClient(ClientRepository $clientRepository, $id): Response
    {
        $clients = $clientRepository->find($id);
        return $this->json($clients, 200, [], ['groups' => 'client:read']);
    }

    /**
     * @Route("/api/clients/{id}/users", name="usersByClient", methods={"GET"})
     */
    public function getUsersByClient(UserRepository $userRepository, $id): Response
    {
        $users = $userRepository->findBy(['client_id' => $id]);
        return $this->json($users, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/user", name="post_user", methods={"POST"})
     */
    public function postUserByClient(EntityManagerInterface $em, Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder, ClientRepository $clientRepository): Response
    {
        try {
            $jsonReceived = $request->getContent();
            $user = $serializerInterface->deserialize($jsonReceived, User::class, 'json', ['groups' => 'user:read']);
            $plainPassword = $user->getPassword();
            $encoded = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }
            $em->persist($user);
            $em->flush();
            return $this->json($user, 200, [], ['groups' => 'user:read']);
        } catch (NotEncodableValueException $e) {
            return $this->json(['code' => 400, 'message' => $e->getMessage()], 400);
        }
    }
    /**
     * @Route("/api/delete/{id}", name="delete_user", methods={"DELETE"})
     */
    public function DeleteUser(EntityManagerInterface $em, Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validator, $id): Response
    {
        $user = $em->find(User::class, $id);
        $em->remove($user);
        $em->flush();
        return $this->json($user, 200);
    }
}
