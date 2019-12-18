<?php
namespace Solcre\Pokerclub\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Solcre\Pokerclub\Repository\SessionRepository")
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

    // protected $countActivePlayers;


    protected $activePlayers;


    protected $distinctPlayers;


    protected $seatedPlayers;


    protected $commissionTotal;


    protected $expensesTotal;


    protected $dealerTipTotal;


    protected $serviceTipTotal;


    protected $totalCashout;


    protected $totalPlayed;


    protected $valid;


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
     * @ORM\OneToMany(targetEntity="Solcre\Pokerclub\Entity\CommissionSessionEntity", mappedBy="session")
     */
    protected $sessionCommissions;


    /**
     * @ORM\OneToMany(targetEntity="Solcre\Pokerclub\Entity\ExpensesSessionEntity", mappedBy="session")
     */
    protected $sessionExpenses;


    /**
     * @ORM\Column(type="string", name="rakeback_class")
     */
    protected $rakebackClass;


    /**
     * @ORM\Column(type="float", name="minimum_user_session_minutes")
     */
    protected $minimumUserSessionMinutes;

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
        $minimumUserSessionMinutes = null
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
        $this->setMinimumUserSessionMinutes($minimumUserSessionMinutes);
        $this->sessionExpenses    = new ArrayCollection();
        $this->sessionCommissions = new ArrayCollection();
        $this->sessionUsers       = new ArrayCollection();
        $this->sessionDealerTips  = new ArrayCollection();
        $this->sessionServiceTips = new ArrayCollection();
    }

    // @codeCoverageIgnoreStart
    public function getId()
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id=$id;
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date): self
    {
        $this->date=$date;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title): self
    {
        $this->title=$title;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): self
    {
        $this->description=$description;
        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): self
    {
        $this->photo=$photo;
        return $this;
    }

    public function getSeats()
    {
        return $this->seats;
    }

    public function setSeats($seats): self
    {
        $this->seats=$seats;
        return $this;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setStartTime($startTime): self
    {
        $this->startTime=$startTime;
        return $this;
    }

    public function getStartTimeReal()
    {
        return $this->startTimeReal;
    }

    public function setStartTimeReal($startTimeReal): self
    {
        $this->startTimeReal=$startTimeReal;
        return $this;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function setEndTime($endTime): self
    {
        $this->endTime=$endTime;
        return $this;
    }

    public function getMinimumUserSessionMinutes()
    {
        return $this->minimumUserSessionMinutes;
    }

    public function setMinimumUserSessionMinutes($minimumUserSessionMinutes = null): self
    {
        $this->minimumUserSessionMinutes = $minimumUserSessionMinutes;
        return $this;
    }

    public function getSessionDealerTips()
    {
        return $this->sessionDealerTips;
    }

    public function setSessionDealerTips($dealerTips): self
    {
        $this->sessionDealerTips=$dealerTips;
        return $this;
    }

    public function getSessionServiceTips()
    {
        return $this->sessionServiceTips;
    }

    public function setSessionServiceTips($serviceTips): self
    {
        $this->sessionServiceTips=$serviceTips;
        return $this;
    }

    public function getSessionUsers()
    {
        return $this->sessionUsers;
    }

    public function setSessionUsers($sessionUsers): self
    {
        $this->sessionUsers=$sessionUsers;
        return $this;
    }

    public function getSessionCommissions()
    {
        return $this->sessionCommissions;
    }

    public function setSessionCommissions($sessionCommissions): self
    {
        $this->sessionCommissions = $sessionCommissions;

        return $this;
    }

    public function getSessionExpenses()
    {
        return $this->sessionExpenses;
    }

    public function setSessionExpenses($sessionExpenses): self
    {
        $this->sessionExpenses=$sessionExpenses;

        return $this;
    }

    public function getRakebackClass()
    {
        return $this->rakebackClass;
    }

    public function setRakebackClass($rakebackClass = null): self
    {
        $this->rakebackClass=$rakebackClass;

        return $this;
    }

    public function getBuyins(): ?array
    {
        return array_reduce(
            $this->sessionUsers->toArray(),
            static function ($buyins, UserSessionEntity $userSession) {
                if (! is_array($buyins)) {
                    $buyins = [];
                }
                return array_merge($buyins, $userSession->getBuyins()->toArray());
            },
            []
        );
    }

    public function getTotalCashout(): ?int
    {
        return array_reduce(
            $this->sessionUsers->toArray(),
            static function ($cashout, UserSessionEntity $user) {
                return $cashout + $user->getCashout();
            },
            0
        );
    }

    public function getDealerTipTotal(): ?int
    {
        return array_reduce(
            $this->sessionDealerTips->toArray(),
            static function ($dealerTipTotal, DealerTipSessionEntity $tipHour) {
                return $dealerTipTotal + $tipHour->getDealerTip();
            },
            0
        );
    }

    public function getExpensesTotal(): ?int
    {
        return array_reduce(
            $this->sessionExpenses->toArray(),
            static function ($expensesTotal, ExpensesSessionEntity $expenditure) {
                return $expensesTotal + $expenditure->getAmount();
            },
            0
        );
    }

    public function getServiceTipTotal(): ?int
    {
        return array_reduce(
            $this->sessionServiceTips->toArray(),
            static function ($serviceTipTotal, ServiceTipSessionEntity $tipHour) {
                return $serviceTipTotal + $tipHour->getServiceTip();
            },
            0
        );
    }

    public function getCommissionTotal(): ?int
    {
        return array_reduce(
            $this->sessionCommissions->toArray(),
            static function ($commissionTotal, CommissionSessionEntity $commissionHour) {
                return $commissionTotal + $commissionHour->getCommission();
            },
            0
        );
    }

    public function getTotalPlayed(): ?int
    {
        return array_reduce(
            $this->getBuyins(),
            static function ($amountTotal, BuyinSessionEntity $buyin) {
                return $amountTotal +
                    $buyin->getAmountCash() +
                    $buyin->getAmountCredit();
            },
            0
        );
    }

    public function getValid(): bool
    {
        $total = $this->getTotalCashout() +
            $this->getCommissionTotal() +
            $this->getDealerTipTotal() +
            $this->getServiceTipTotal();

        return $this->getTotalPlayed() === $total;
    }

    public function getActivePlayers(): array
    {
        $activePlayers = [];

        /** @var UserSessionEntity $user */
        foreach ($this->sessionUsers as $user) {
            if (($user->getStart() !== null) &&
                ($user->getEnd() === null) &&
                (! in_array($user->getUser()->getId(), $activePlayers, true))) {
                $activePlayers[]= $user->getUser()->getId();
            }
        }

        return $activePlayers;
    }

    public function getSeatedPlayers(): array
    {
        $seatedPlayers = [];

        /** @var UserSessionEntity $user */
        foreach ($this->sessionUsers as $user) {
            if (($user->getEnd() === null) &&
                (! in_array($user->getUser()->getId(), $seatedPlayers, true))) {
                $seatedPlayers[]= $user->getUser()->getId();
            }
        }

        return $seatedPlayers;
    }

    public function getDistinctPlayers(): array
    {
        $distinctPlayers = [];

        /** @var UserSessionEntity $user */
        foreach ($this->sessionUsers as $user) {
            if (! in_array($user->getUser()->getId(), $distinctPlayers, true)) {
                $distinctPlayers[]= $user->getUser()->getId();
            }
        }

        return $distinctPlayers;
    }

    public function getAveragePlayersInPeriod(\DateTime $from, \DateTime $to)
    {
        $players = 0;
        $numberUsers = 0;
        $usersSession = $this->getSessionUsers();

        /** @var UserSessionEntity $userSession */
        foreach ($usersSession as $userSession) {
            $players += $userSession->getPercentagePlayed($from, $to);
            $numberUsers++;
        }

        return $players/100;
    }

    public function toArray(): array
    {
        $ret = [
            'id'                        => $this->getId(),
            'created_at'                => $this->getDate(),
            'title'                     => $this->getTitle(),
            'description'               => $this->getDescription(),
            'startTime'                 => $this->getStartTime(),
            'startTimeReal'             => $this->getStartTimeReal(),
            'countActivePlayers'        => count($this->getActivePlayers()),
            'activePlayers'             => $this->getActivePlayers(),
            'distinctPlayers'           => $this->getDistinctPlayers(),
            'countSeatedPlayers'        => count($this->getSeatedPlayers()),
            'seats'                     => $this->getSeats(),
            'endTime'                   => $this->getEndTime(),
            'commissionTotal'           => $this->getCommissionTotal(),
            'expensesTotal'             => $this->getExpensesTotal(),
            'dealerTipTotal'            => $this->getDealerTipTotal(),
            'serviceTipTotal'           => $this->getServiceTipTotal(),
            'rakebackClass'             => $this->getRakebackClass(),
            'totalCashout'              => $this->getTotalCashout(),
            'totalPlayed'               => $this->getTotalPlayed(),
            'valid'                     => $this->getValid(),
            'minimumUserSessionMinutes' => (int)$this->getMinimumUserSessionMinutes()
        ];

        return $ret;
    }
}
