<?php

namespace App\EventSubscriber;

use App\Entity\Comment;
use App\Entity\User;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $encoder)
    {
        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => [
                ["addUser",255],
            ]
        ];
    }

    /**
     * Encode user's password when it is created whith easyadmin
     * @param BeforeEntityPersistedEvent $event
     * @return void
     */
    public function addUser(BeforeEntityPersistedEvent $event){
        $entity = $event->getEntityInstance();

        if(!$entity instanceof User){
            return;
        }
        $this->encodePassword($entity);
    }

    /**
     * Function to hash password
     * @param User $user
     * @return void
     */
    private function encodePassword(User $user)
    {
        $pass = $user->getPassword();
        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $pass
            )
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}