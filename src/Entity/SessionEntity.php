<?php
namespace Solcre\Pokerclub\Entity;

use Solcre\Pokerclub\Exception\UserAlreadyAddedException;
use Solcre\Pokerclub\Exception\SessionFullException;
use Solcre\Pokerclub\Exception\PlayerNotFoundException;
use Solcre\Pokerclub\Exception\InsufficientBuyinException;
use Solcre\Pokerclub\Exception\ComissionAlreadyAddedException;
use Solcre\Pokerclub\Exception\DealerTipAlreadyAddedException;
use Solcre\Pokerclub\Exception\ServiceTipAlreadyAddedException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Solcre\Pokerclub\Rakeback\RakeBackAlgorithm;

/**
 * @ORM\Entity(repositoryClass="Solcre\Pokerclub\Repository\BaseRepository")
 * @ORM\Table(name="sessions")
 */
class SessionEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $date;


    /**
     * @ORM\Column(type="string")
     */
    protected $title;


    /**
     * @ORM\Column(type="string")
     */
    protected $description;


    protected $photo;


    /**
     * @ORM\Column(type="integer", name="count_of_seats")
     */
    protected $seats;


    /**
     * @ORM\Column(type="datetime", name="start_at")
     */
    protected $startTime;


    /**
     * @ORM\Column(type="datetime", name="real_start_at")
     */
    protected $startTimeReal;


    /**
     * @ORM\Column(type="datetime", name="end_at")
     */
    protected $endTime;


    /**
     * @ORM\OneToMany(targetEntity="Solcre\Pokerclub\Entity\DealerTipSessionEntity", mappedBy="session")
     */
    protected $sessionDealerTips;


    /**
     * @ORM\OneToMany(targetEntity="Solcre\Pokerclub\Entity\ServiceTipSessionEntity", mappedBy="session")
     */
    protected $sessionServiceTips;


    /**
     * @ORM\OneToMany(targetEntity="Solcre\Pokerclub\Entity\UserSessionEntity", mappedBy="session")
     */
    protected $sessionUsers;


    /**
     * @ORM\OneToMany(targetEntity="Solcre\Pokerclub\Entity\ComissionSessionEntity", mappedBy="session")
     */
    protected $sessionComissions;


    /**
     * @ORM\OneToMany(targetEntity="Solcre\Pokerclub\Entity\ExpensesSessionEntity", mappedBy="session")
     */
    protected $sessionExpenses;


    /**
     * @ORM\Column(type="string", name="rakeback_class")
     */
    protected $rakebackClass;


    /**
     * @ORM\Column(type="float", name="minimum_user_session_hours")
     */
    protected $minimumUserSessionHours;

    public function __construct(
        $id = null,
        \DateTime $date = null,
        $title = null,
        $description = null,
        $photo = null,
        $seats = null,
        $startTime = null,
        $startTimeReal = null,
        $endTime = null,
        $rakebackClass = null,
        $minimumUserSessionHours = null
    ) {
        $this->setId($id);
        $this->setDate($date);
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setPhoto($photo);
        $this->setSeats($seats);
        $this->setStartTime($startTime);
        $this->setStartTimeReal($startTimeReal);
        $this->setEndTime($endTime);
        $this->setRakebackClass($rakebackClass);
        $this->setMinimumUserSessionHours($minimumUserSessionHours);
        $this->sessionExpenses    = new ArrayCollection();
        $this->sessionComissions  = new ArrayCollection();
        $this->sessionUsers       = new ArrayCollection();
        $this->sessionDealerTips  = new ArrayCollection();
        $this->sessionServiceTips = new ArrayCollection();
    }

    // @codeCoverageIgnoreStart
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id=$id;
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date=$date;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title=$title;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description=$description;
        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo)
    {
        $this->photo=$photo;
        return $this;
    }

    public function getSeats()
    {
        return $this->seats;
    }

    public function setSeats($seats)
    {
        $this->seats=$seats;
        return $this;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setStartTime($startTime)
    {
        $this->startTime=$startTime;
        return $this;
    }

    public function getStartTimeReal()
    {
        return $this->startTimeReal;
    }

    public function setStartTimeReal($startTimeReal)
    {
        $this->startTimeReal=$startTimeReal;
        return $this;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function setEndTime($endTime)
    {
        $this->endTime=$endTime;
        return $this;
    }

    public function getMinimumUserSessionHours()
    {
        return $this->minimumUserSessionHours;
    }

    public function setMinimumUserSessionHours($minimumUserSessionHours = null)
    {
        $this->minimumUserSessionHours = $minimumUserSessionHours;
        return $this;
    }

    public function getSessionDealerTips()
    {
        return $this->sessionDealerTips;
    }

    public function setSessionDealerTips($dealerTips)
    {
        $this->sessionDealerTips=$dealerTips;
        return $this;
    }

    public function getSessionServiceTips()
    {
        return $this->sessionServiceTips;
    }

    public function setSessionServiceTips($serviceTips)
    {
        $this->sessionServiceTips=$serviceTips;
        return $this;
    }

    public function getSessionUsers()
    {
        return $this->sessionUsers;
    }

    public function setSessionUsers($sessionUsers)
    {
        $this->sessionUsers=$sessionUsers;
        return $this;
    }

    public function getSessionComissions()
    {
        return $this->sessionComissions;
    }

    public function setSessionComissions($sessionComissions)
    {
        $this->sessionComissions=$sessionComissions;
        return $this;
    }

    public function getSessionExpenses()
    {
        return $this->sessionExpenses;
    }
    
    public function setSessionExpenses($sessionExpenses)
    {
        $this->sessionExpenses=$sessionExpenses;
        return $this;
    }

    public function getRakebackClass()
    {
        return $this->rakebackClass;
    }
    
    public function setRakebackClass($rakebackClass = null)
    {
        $this->rakebackClass=$rakebackClass;
        return $this;
    }


    public function getBuyins()
    {
        return array_reduce(
            $this->sessionUsers->toArray(),
            function ($buyins, $userSession) {
                if (!is_array($buyins)) {
                    $buyins = [];
                }
                return array_merge($buyins, $userSession->getBuyins()->toArray());
            },
            []
        );
    }

    public function getTotalCashout()
    {
        return array_reduce(
            $this->sessionUsers->toArray(),
            function ($cashout, $user) {
                return $cashout + $user->getCashout();
            },
            0
        );
    }

    public function getDealerTipTotal()
    {
        return array_reduce(
            $this->sessionDealerTips->toArray(),
            function ($dealerTipTotal, $tipHour) {
                return $dealerTipTotal + $tipHour->getDealerTip();
            },
            0
        );
    }

    public function getExpensesTotal()
    {
        return array_reduce(
            $this->sessionExpenses->toArray(),
            function ($expensesTotal, $expenditure) {
                return $expensesTotal + $expenditure->getAmount();
            },
            0
        );
    }

    public function getServiceTipTotal()
    {
        return array_reduce(
            $this->sessionServiceTips->toArray(),
            function ($serviceTipTotal, $tipHour) {
                return $serviceTipTotal + $tipHour->getServiceTip();
            },
            0
        );
    }

    public function getComissionTotal()
    {
        return array_reduce(
            $this->sessionComissions->toArray(),
            function ($comissionTotal, $comissionHour) {
                return $comissionTotal + $comissionHour->getComission();
            },
            0
        );
    }

    public function getTotalPlayed()
    {
        return array_reduce(
            $this->getBuyins(),
            function ($amountTotal, $buyin) {
                return $amountTotal +
                $buyin->getAmountCash() +
                $buyin->getAmountCredit();
            },
            0
        );
    }


    public function getValid()
    {
        $total = $this->getTotalCashout() +
        $this->getComissionTotal() +
        $this->getDealerTipTotal()+
        $this-> getServiceTipTotal();
        return $this->getTotalPlayed() == $total;
    }


/*
    public function validateSession($session)
    {
        $total = $session->getTotalCashout() +
        $session->getComissionTotal() +
        $session->getDealerTipTotal()+
        $session-> getServiceTipTotal();
        return $session->getTotalPlayed() == $total;
    }
*/

    public function getActivePlayers()
    {
        $activePlayers = [];
        foreach ($this->sessionUsers as $user) {
            if (!in_array($user->getUser()->getId(), $activePlayers) &&
                ($user->getEnd() == null) &&
                ($user->getStart() != null)) {
                $activePlayers[]= $user->getUser()->getId();
            }
        }
        return $activePlayers;
    }

    public function getSeatedPlayers()
    {
        $seatedPlayers = [];
        foreach ($this->sessionUsers as $user) {
            if (!in_array($user->getUser()->getId(), $seatedPlayers) &&
                ($user->getEnd() == null)) {
                $seatedPlayers[]= $user->getUser()->getId();
            }
        }
        return $seatedPlayers;
    }

    public function getDistinctPlayers()
    {
        $distinctPlayers = [];
        foreach ($this->sessionUsers as $user) {
            if (!in_array($user->getUser()->getId(), $distinctPlayers)) {
                $distinctPlayers[]= $user->getUser()->getId();
            }
        }
        return $distinctPlayers;
    }
    /*
    public function calculatePoints()
    {
        foreach ($this->sessionUsers as $userSession) {
            $userSession->setAccumulatedPoints($this->rakebackAlgorithm->calculate($userSession));
        }
    }
    */

    public function getAveragePlayersInPerdiod(\DateTime $from, \DateTime $to)
    {
        $players = 0;
        $numberUsers = 0;
        $usersSession = $this->getSessionUsers();
        foreach ($usersSession as $userSession) {
               $players += $userSession->getPercentagePlayed($from, $to);
               $numberUsers++;
        }

        return $players/100;
    }

    public function toArray()
    {
        $ret = [
        'id'                      => $this->getId(),
        'created_at'              => $this->getDate(),
        'title'                   => $this->getTitle(),
        'description'             => $this->getDescription(),
        'startTime'               => $this->getStartTime(),
        'startTimeReal'           => $this->getStartTimeReal(),
        'countActivePlayers'      => count($this->getActivePlayers()),
        'activePlayers'           => $this->getActivePlayers(),
        'distinctPlayers'         => $this->getDistinctPlayers(),
        'countSeatedPlayers'      => count($this->getSeatedPlayers()),
        'seats'                   => $this->getSeats(),
        'endTime'                 => $this->getEndTime(),
        'comissionTotal'          => $this->getComissionTotal(),
        'expensesTotal'           => $this->getExpensesTotal(),
        'dealerTipTotal'          => $this->getDealerTipTotal(),
        'serviceTipTotal'         => $this->getServiceTipTotal(),
        'rakebackClass'           => $this->getRakebackClass(),
        'totalCashout'            => $this->getTotalCashout(),
        'totalPlayed'             => $this->getTotalPlayed(),
        'valid'                   => $this->getValid(),
        'minimumUserSessionHours' => (float)$this->getMinimumUserSessionHours()
        ];

       /* foreach ($this->sessionUsers as $userSession) {
            if ($userSession instanceof UserSessionEntity) {
                $ret['usersSession'][] = $userSession->toArray();
            }
        };  */

        return $ret;
    }
}
