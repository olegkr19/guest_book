<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

//    /**
//     * @return Message[] Returns an array of Message objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
    * @return array Returns an array
    */
    public function findAllMessages(string $sortBy = 'id', string $sortOrder = 'desc'): array
    {

        $entityManager = $this->getEntityManager();

        $conn = $entityManager->getConnection();

        $sql = 'SELECT id, username, email, homepage, text, created_at FROM message WHERE coordination = 1 ORDER BY ' . $sortBy . ' ' . $sortOrder;

        // Create a prepared statement
        $statement = $conn->prepare($sql);

        return $statement->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return array Returns an array
     */
    public function findAllMessagesByUser(User $user, string $sortBy = 'id', string $sortOrder = 'desc')
    {
        $entityManager = $this->getEntityManager();

        $conn = $entityManager->getConnection();

        $where = 'email = :email AND coordination = 1';

        $prepareData = [
            'email' => $user->getUserIdentifier()
        ];
        
        $sql = '
            SELECT id, username, email, homepage, text, created_at
            FROM message
            WHERE '. $where .'
            ORDER BY ' . $sortBy . ' ' . $sortOrder
        ;

        return $conn->prepare($sql)->executeQuery($prepareData)->fetchAllAssociative();
    }
}
