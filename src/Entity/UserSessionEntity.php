<?php
namespace Solcre\lmsuy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Embeddable
 * @ORM\Entity(repositoryClass="Solcre\lmsuy\Repository\BaseRepository")
 * @ORM\Table(name="sessions_users")
 */
class UserSessionEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="Solcre\lmsuy\Entity\SessionEntity", inversedBy="sessionUsers")
     * @ORM\JoinColumn(name="session_id",                               referencedColumnName="id")
     */
    protected $session;


    protected $idUser;


    /**
     * @ORM\Column(type="integer",name="is_approved")
     */
    protected $isApproved;


    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, name="points")
     */
    protected $accumulatedPoints;


    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    protected $cashout;


    /**
     * @ORM\Column(type="datetime", name="start_at")
     */
    protected $start;


    /**
     * @ORM\Column(type="datetime", name="end_at")
     */
    protected $end;



       /**
        * @ORM\ManyToOne(targetEntity="Solcre\lmsuy\Entity\UserEntity", inversedBy="sessionUsers")
        * @ORM\JoinColumn(name="user_id",                               referencedColumnName="id")
        */
    protected $user;


    /**
     * One User Session has many Buyins. This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Solcre\lmsuy\Entity\BuyinSessionEntity", mappedBy="userSession")
     */
    protected $buyins;


    public function __construct(
        $id = null,
        SessionEntity $session = null,
        $idUser = null,
        $isApproved = null,
        $accumulatedPoints = 0,
        $cashout = null,
        $start = null,
        $end = null,
        UserEntity $user = null
    ) {
        $this->setId($id);
        $this->setSession($session);
        $this->setIdUser($idUser);
        $this->setIsApproved($isApproved);
        $this->setAccumulatedPoints($accumulatedPoints);
        $this->setCashout($cashout);
        $this->setStart($start);
        $this->setEnd($end);
        $this->setUser($user);
        $this->buyins = new ArrayCollection();
    }
    
    // @codeCoverageIgnoreStart
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function getSession()
    {
        return $this->session;
    }
    
    public function setSession(SessionEntity $session = null)
    {
        return $this->session = $session;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }
    
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
        return $this;
    }
    
    public function getIsApproved()
    {
        return $this->isApproved;
    }
    
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;
        return $this;
    }
    
    public function getAccumulatedPoints()
    {
        return $this->accumulatedPoints;
    }
    
    public function setAccumulatedPoints($accumulatedPoints)
    {
        $this->accumulatedPoints = $accumulatedPoints;
        return $this;
    }
    
    public function getCashout()
    {
        return $this->cashout;
    }
    
    public function setCashout($cashout)
    {
        $this->cashout = $cashout;
        return $this;
    }
    
    public function getStart()
    {
        return $this->start;
    }
    
    public function setStart($start)
    {
        $this->start = $start;
        return $this;
    }
    
    public function getEnd()
    {
        return $this->end;
    }
    
    public function setEnd($end)
    {
        $this->end = $end;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser(UserEntity $user = null)
    {
        $this->user = $user;
        return $this;
    }

    public function getBuyins()
    {
        return $this->buyins;
    }
    
    public function setBuyins($buyins)
    {
        $this->buyins = $buyins;
        return $this;
    }
    // @codeCoverageIgnoreEnd

    public function getCashin()
    {
        $cashin = 0;
            $buyins = $this->getBuyins()->toArray();

        foreach ($buyins as $buyin) {
                $cashin += $buyin->getAmountCash() + $buyin->getAmountCredit();
        }
       
        return $cashin;
    }

    public function getTotalCredit()
    {
        $credit = 0;
        $buyins = $this->getBuyins()->toArray();

        foreach ($buyins as $buyin) {
            $credit += $buyin->getAmountCredit();
        }
        return $credit;
    }

    public function getResult()
    {
        return $this->getCashout() - $this->getCashin();
    }

    public function getDuration()
    {
        $date1          = $this->getStart();
        $date2          = $this->getEnd();
        $minutes        = date_diff($date1, $date2)->format('%i');
        
        $roundedMinutes = floor((($minutes/60)/.25))*.25;
        $hours          = date_diff($date1, $date2)->format('%h') + $roundedMinutes;
        $days = date_diff($date1, $date2)->format('%d');
        if ((int)$days > 0) {
            $hours += (int)$days * 24;
        }
        return $hours;
    }

    public function inMinutes(\DateTime $time1, \DateTime $time2)
    {
        $minutes = (int)date_diff($time1, $time2)->format('%i');
        $hours   = (int)date_diff($time1, $time2)->format('%h');
        $days    = (int)date_diff($time1, $time2)->format('%d');

        if ($days > 0) {
            $minutes += $days * 1440;
        }

        if ($hours > 0) {
            $minutes += $hours * 60;
        }

        return $minutes;
    }


    public function getPercentagePlayed(\DateTime $from, \DateTime $to)
    {
        $start = $this->getStart();
        $end = $this->getEnd();

        // case 0% - before or after
        if ((($from < $start) && ($to < $start)) || ($from > $end)) {
            return 0;
        } elseif (($from >= $start) && ($to <= $end)) {
        //case 100% - enclosing & enclosing %% touching
            return 100;
        } else {
        // other cases
            $sample = $this->inMinutes($from, $to);
            // $sample represent 100%

            if (($from <= $start) && ($to <= $end)) {
            // start is inside & start is inside && end touching
                $fraction = $this->inMinutes($to, $start);
            } 
            elseif (($from >= $start) && ($to > $end)) {
            // end is inside & end is inside && start touching
                $fraction = $this->inMinutes($from, $end); //from end
            } 
            elseif (($from < $start) && ($to > $end)) {
            // Inside start and end
                $fraction = $this->inMinutes($end, $start);
            }
            
            return ($fraction * 100) / $sample;
        }
    }

        
    public function toArray()
    {
        
        $ret =  [
        'id'          => $this->getId(),
        'idSession'   => $this->getSession()->getId(),
        'idUser'      => $this->getIdUser(),
        'isApproved'  => $this->getIsApproved(),
        'cashout'     => $this->getCashout(),
        'startTime'   => $this->getStart(),
        'endTime'     => $this->getEnd(),
        'cashin'      => $this->getCashin(),
        'totalCredit' => $this->getTotalCredit()

        ];
        
        $user = $this->getUser();
        if ($user instanceof UserEntity) {
            $ret['user'] = $user->toArray();
        }
        $session = $this->getSession();
        
        if ($session instanceof SessionEntity) {
            $ret['session'] = $session->toArray();
        }
        
        return $ret;
    }
}