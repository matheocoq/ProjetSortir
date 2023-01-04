<?php

namespace App\Repository;

use App\Entity\Sorties;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sorties>
 *
 * @method Sorties|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sorties|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sorties[]    findAll()
 * @method Sorties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sorties::class);
    }

    public function save(Sorties $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sorties $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Sorties[] Returns an array of Sorties objects
     */
    public function findByRecherche($param,$user): array
    {
        $date= new DateTime();
        $query=  $this->createQueryBuilder('s');
        if ($param->get("nom_site")!="") {
            $query->innerJoin('s.organisateur', 'o');
            $query->andWhere('o.sites = :site');
            $query->setParameter('site',$param->get("nom_site"));
        }
        if ($param->get("nom_sortie_contient")!="") {
            $query->andWhere("s.nom LIKE :nom_sortie");
            $query->setParameter('nom_sortie',"%".$param->get("nom_sortie_contient")."%");
        }
        if ($param->get("date_debut")!="") {
            $query->andWhere("s.date_debut >= :date_debut ");
            $query->setParameter('date_debut',$param->get("date_debut"));
           
        }
        if ($param->get("date_fin")!="") {
            $query->andWhere("s.date_debut <= :date_fin");
            $query->setParameter('date_fin',$param->get("date_fin"));
        }
        if ($param->get("sortie_orga")!=null) {
            $query->andWhere("s.organisateur = :organ");
            $query->setParameter('organ',$user->getId());
        }
        if ($param->get("sortie_insc")!=null || $param->get("sortie_n_insc")!=null) {
           
            if ($param->get("sortie_insc")!=null && $param->get("sortie_n_insc")==null) {
                $query->innerJoin('s.inscriptions', 'i');
                $query->andWhere('i.participant = :participant');
                $query->setParameter('participant',$user->getId());
            }
            else {
               if ($param->get("sortie_insc")==null && $param->get("sortie_n_insc")!=null) {
                    $query->leftJoin('s.inscriptions', 'i');
                    $query->andWhere('i.participant != :participant or i.participant is null');
                    $query->setParameter('participant',$user->getId());
               }
            }
        }
        if ($param->get("sortie_passee")!=null) {
            $query->andWhere("s.date_debut < :finie ");
            $query->setParameter('finie',$date);
        }
        $query->orderBy('s.date_debut', 'ASC');
        $requete=$query->getQuery();
        return $requete->getResult();
    }

//    /**
//     * @return Sorties[] Returns an array of Sorties objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sorties
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
