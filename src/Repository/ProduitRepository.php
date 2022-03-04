<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Produit;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\OrderBy;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Produit $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Produit $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getProduitsByUser(User $user)
    {
        return $this->createQueryBuilder('produit')
            ->andWhere('produit.auteur = :id') // on recupère l'author dans l'entité Comments grâce à son :id
            ->setParameter('id' , $user)
            ->getQuery()
            ->getResult();
    }
    public function getAllByOrder()
    {
        return $this->createQueryBuilder('produit')
        ->orderBy('produit.nom', 'ASC')  // 'ASC' pour ascendant 'DESC' descendant
        ->getQuery()
        ->getResult();
    }
    public function getProduitByName($saisie)
    {
        return $this->createQueryBuilder('produit')  // met une valeur ramdom
                    ->andWhere('produit.nom LIKE :saisie' ) // condition de requête  valeur.ce qu'on veut like car il recup ce qui ressemble à la saisie  :saisie = valeur de la saisie
                    ->setParameter('saisie', "%$saisie%")    // recup saisie   % % pour que le mot saisie soit recherché dans les nom (contenu)
                    ->orderBy('produit.nom', 'ASC')  // on récup la valeur puis l'ordre dans lequel on rend l'info ASC again 
                    ->getQuery()
                    ->getResult();
    }




    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
