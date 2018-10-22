<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;


/**
 * CaseCreditRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CaseCreditRepository extends \Doctrine\ORM\EntityRepository
{
	public function addWhereClause(&$qb,&$params){

        $params = array_filter($params,function($el){
            if(is_array($el)){
                return $el;
            }
            return strip_tags(trim($el));
        });

		// recherche par terms
		if(@$params["q"]){
			$this->whereTerms($qb,@$params["q"]);
		}

		// recherche par id
		if(@$params["id"]){
			$this->whereId($qb,@$params["id"]);
		}

		// recherche par fbo_id
		if(@$params["fbo_id"]){
			$this->whereFboId($qb,@$params["fbo_id"]);
		}

		// recherche par fbo_code
		if(@$params["idFbo"]){
			$this->whereFboCode($qb,@$params["idFbo"]);
		}

		// recherche par admin_id
		if(@$params["admin_id"]){
			$this->whereAdminId($qb,@$params["admin_id"]);
		}

		// recherche par admin_code
		if(@$params["admin_code"]){
			$this->whereAdminCode($qb,@$params["admin_code"]);
		}


		// recherche par année
        if(@$params["date"] && @$params["dateFin"]){
            $this->whereDateRange($qb,$params["date"],$params["dateFin"]);
        }
        else if(@$params["date"]){
            $this->whereDate($qb,$params["date"]);
        }

        return $this;
    }


	/**
	 * permet de retourner tous les elements de l'arbre
	 * 
	 * @param array $params les pararatres de la recherche
	 * @param integer $limit le nombre de resultat à retourner
	 * @param integer $offset la position de bebut de cette recherche
	 */
	public function search($params = array(),$limit = 50,$offset=0){
		$qb = $this->createQueryBuilder("cc")
		->innerJoin("cc.fbo","fbo")
		->leftJoin("cc.admin","admin");

		$this->addWhereClause($qb,$params);

		// ordre d'affichage par id
        if(@$params['order_id']){
            $order = strtoupper(trim($params['order_id'])) == "ASC" ? "ASC" : "DESC";
            $qb->orderBy("cc.id",$order);
        }

		if(!@$params['order_id'] && !@$params["order_name"]){
    		$params["order_name"] = "asc";
    	}

		// ordre d'affichage par nom de programme
		if(@$params['order_name']){
			$order = strtoupper(trim($params['order_name'])) == "ASC" ? "ASC" : "DESC";
			$qb->orderBy("fbo.username",$order);
		}

		// ordre d'affichage par date e production
		if(@$params['order_year']){
			$order = strtoupper(trim($params['order_year'])) == "ASC" ? "ASC" : "DESC";
			$qb->orderBy("cc.date",$order);
		}

	    // limit et offset
        if($limit != -1){
            $qb
            ->setFirstResult( $offset )
            ->setMaxResults( $limit );
        }

   		$query = $qb->getQuery();

	    return $query->getResult();
	}
	

	public function whereTerms(QueryBuilder $qb,$value){
		$qb->andWhere($qb->expr()->orX(
			$qb->expr()->like("fbo.username", ":q"),
			$qb->expr()->like("fbo.email", ":q")
		))
	    ->setParameter("q","%$value%");
	}

	public function whereId(QueryBuilder $qb,$value){
		$qb->andWhere($qb->expr()->eq("cc.id", ":id"))
	    ->setParameter("id",$value);
	}

	public function whereFboId(QueryBuilder $qb,$value){
		$qb->andWhere($qb->expr()->eq("fbo.id", ":fbo_id"))
	    ->setParameter("fbo_id",$value);
	}

	public function whereFboCode(QueryBuilder $qb,$value){
		$qb->andWhere($qb->expr()->eq("fbo.code", ":fbo_code"))
	    ->setParameter("fbo_code",$value);
	}

	public function whereAdminId(QueryBuilder $qb,$value){
		$qb->andWhere($qb->expr()->eq("admin.id", ":admin_id"))
	    ->setParameter("admin_id",$value);
	}

	public function whereAdminCode(QueryBuilder $qb,$value){
		$qb->andWhere($qb->expr()->eq("admin.code", ":admin_code"))
	    ->setParameter("admin_code",$value);
	}

	public function whereDate(QueryBuilder $qb,$value){
        $value = new \Datetime($value);

        $qb->andWhere($qb->expr()->eq("DATE_FORMAT(cc.date,'%Y-%m-%d')",":year_start"))
        ->setParameter("year_start",$value->format("Y-m-d"));
    }

	public function whereDateRange(QueryBuilder $qb,$start,$end){

        $start = new \Datetime($start);
        $end = new \Datetime($end);

		$qb->andWhere(
			$qb->expr()->between("DATE_FORMAT(cc.date,'%Y-%m-%d')",":year_start",":year_end")
		)
        ->setParameter("year_start",$start->format("Y-m-d"))
        ->setParameter("year_end",$end->format("Y-m-d"));
  	}

  	public function whereYear(QueryBuilder $qb,$value){
        $qb->andWhere($qb->expr()->eq("DATE_FORMAT(cc.date,'%Y')",":year"))
        ->setParameter("year",$value);
    }
    public function whereMonth(QueryBuilder $qb,$value){
        $qb->andWhere($qb->expr()->eq("MONTH(cc.date,'%Y')",":month"))
        ->setParameter("month",$value);
    }

	public function count(array $params = array() ){
        $qb = $this->createQueryBuilder('cc')
        ->innerJoin("cc.fbo","fbo")
        ->leftJoin("cc.admin","admin");

        $qb
        ->select('count(cc.id)');

        $this->addWhereClause($qb, $params);

        return $qb->getQuery()
        ->getSingleScalarResult();
    }

    public function ccPersonnel(array $params = array() ){
        $qb = $this->createQueryBuilder('cc')
        ->innerJoin("cc.fbo","fbo")
        ->leftJoin("cc.admin","admin");

        $qb
        ->select('sum(cc.value)');

        $this->addWhereClause($qb, $params);

        return $qb->getQuery()
        ->getSingleScalarResult();
    }
}
