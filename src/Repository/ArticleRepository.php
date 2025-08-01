<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function listByPublicationStatus(User $user, bool $statut): array
        //Role: Récupérer tous les articles de l'utilisateur connecté selon leur statut publié ou non
        //Parametre: id de l'Utilisateur connecté
        //Retour: Un tableau d'objet contenant les articles
    {
        return $this->createQueryBuilder('a')   //Alias de l'entité concerné par la recherche (article)
            ->where('a.auteur = :user' )
            ->andWhere('a.parution = :statut')
            ->setParameter('user', $user)
            ->setParameter('statut', $statut)
            ->getQuery()
            ->getResult();
    }
}
