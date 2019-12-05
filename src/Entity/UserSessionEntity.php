<?php
namespace Solcre\Pokerclub\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Solcre\Pokerclub\Exception\UserSessionExceptions;

/**
 * @ORM\Embeddable
 * @ORM\Entity(repositoryClass="Solcre\Pokerclub\Repository\UserSessionRepository")
 * @ORM\Table(name="sessions_users")
 */
class UserSessionEntity
{
    public const PERCENTAGE_100      = 100;
    public const HOURS_DAY           = 24;
    public const MINUTES_OF_ONE_HOUR = 60;
    public const ROUNDING_INTERVAL   = .25;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="Solcre\Pokerclub\Entity\SessionEntity", inversedBy="sessionUsers")
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


    protected $cashin;


    protected $totalCredit;


    protected $totalCash;


    protected $duration;


    /**
     * @ORM\Column(type="datetime", name="start_at")
     */
    protected $start;


    /**
     * @ORM\Column(type="datetime", name="end_at")
     */
    protected $end;


    /**
     * @ORM\Column(type="float", name="minimum_minutes")
     */
    protected $minimumMinutes;


    /**
     * @ORM\ManyToOne(targetEntity="Solcre\Pokerclub\Entity\UserEntity", inversedBy="sessionUsers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;


    /**
     * One User Session has many Buyins. This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Solcre\Pokerclub\Entity\BuyinSessionEntity", mappedBy="userSession")
     */
    protected $buyins;



    public function __construct(
        $id = null,
        SessionEntity $session = null,
        $idUser = null,
        $isApproved = null,
        $accumulatedPoints = 0,
        $cashout = 0,
        $start = null,
        $end = null,
        $minimumMinutes = null,
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
        $this->setMinimumMinutes($minimumMinutes);
        $this->buyins = new ArrayCollection();
    }

    // @codeCoverageIgnoreStart
    public function getId()
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getSession(): SessionEntity
    {
        return $this->session;
    }

    public function setSession(SessionEntity $session = null): self
    {
        $this->session = $session;
        return $this;
    }

    public function getIdUser()
    {
        return ($this->user instanceof UserEntity ? $this->getUser()->getId() : null);
    }

    public function setIdUser($idUser): self
    {
        $this->idUser = $idUser;
        return $this;
    }

    public function getIsApproved()
    {
        return $this->isApproved;
    }

    public function setIsApproved($isApproved): self
    {
        $this->isApproved = $isApproved;
        return $this;
    }

    public function getAccumulatedPoints()
    {
        return $this->accumulatedPoints;
    }

    public function setAccumulatedPoints($accumulatedPoints): self
    {
        $this->accumulatedPoints = $accumulatedPoints;
        return $this;
    }

    public function getCashout()
    {
        return $this->cashout;
    }

    public function setCashout($cashout): self
    {
        $this->cashout = $cashout;
        return $this;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function setStart($start): self
    {
        $this->start = $start;
        return $this;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function setEnd($end): self
    {
        $this->end = $end;
        return $this;
    }

    public function getMinimumMinutes()
    {
        return $this->minimumMinutes;
    }

    public function setMinimumMinutes($minimumMinutes = null): self
    {
        if ($minimumMinutes !== null) {
            $this->minimumMinutes = $minimumMinutes;
        } else {
            $this->session instanceof SessionEntity ?
                $this->minimumMinutes = $this->getSession()->getMinimumUserSessionMinutes() :
                null;
        }

        return $this;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(UserEntity $user = null): self
    {
        $this->user = $user;
        return $this;
    }

    public function getBuyins()
    {
        return $this->buyins;
    }

    public function setBuyins($buyins): self
    {
        $this->buyins = $buyins;
        return $this;
    }
    // @codeCoverageIgnoreEnd

    public function getCashin(): int
    {
        $cashin = 0;
        $buyins = $this->getBuyins()->toArray();

        /** @var BuyinSessionEntity $buyin */
        foreach ($buyins as $buyin) {
            $cashin += $buyin->getAmountCash() + $buyin->getAmountCredit();
        }

        return $cashin;
    }

    public function getTotalCredit(): int
    {
        $credit = 0;
        $buyins = $this->getBuyins()->toArray();

        /** @var BuyinSessionEntity $buyin */
        foreach ($buyins as $buyin) {
            $credit += $buyin->getAmountCredit();
        }

        return $credit;
    }

    public function getTotalCash(): int
    {
        $cash = 0;
        $buyins = $this->getBuyins()->toArray();

        /** @var BuyinSessionEntity $buyin */
        foreach ($buyins as $buyin) {
            $cash += $buyin->getAmountCash();
        }

        return $cash;
    }

    public function getResult(): int
    {
        return $this->getCashout() - $this->getCashin();
    }

    public function getDuration()
    {
        $date1 = $this->getStart();

        if ($date1 === null) {
            return 0;
        }

        $date2    = $this->getEnd() ?? new \DateTime();
        $interval = date_diff($date1, $date2);

        if (! $interval instanceof \DateInterval) {
            throw UserSessionExceptions::InvalidDuration();
        }

        $minutes  = $interval->format('%i');
        $roundedMinutes = floor((($minutes / self::MINUTES_OF_ONE_HOUR) / self::ROUNDING_INTERVAL))
            * self::ROUNDING_INTERVAL;
        $hours          = $interval->format('%h') + $roundedMinutes;
        $days           = $interval->format('%d');

        if ((int)$days > 0) {
            $hours += (int)$days * self::HOURS_DAY;
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
        $end   = $this->getEnd();

        // case 0% - before or after
        if ((($from < $start) && ($to < $start)) || ($from > $end)) {
            return 0;
        } elseif (($from >= $start) && ($to <= $end)) {
            //case 100% - enclosing & enclosing %% touching
            return self::PERCENTAGE_100;
        } else {
            // other cases
            $sample = $this->inMinutes($from, $to);
            // $sample represent 100%

            if (($from <= $start) && ($to <= $end)) {
                // start is inside & start is inside && end touching
                $fraction = $this->inMinutes($to, $start);
            } elseif (($from >= $start) && ($to > $end)) {
                // end is inside & end is inside && start touching
                $fraction = $this->inMinutes($from, $end); //from end
            } elseif (($from < $start) && ($to > $end)) {
                // Inside start and end
                $fraction = $this->inMinutes($end, $start);
            }

            return ($fraction * 100) / $sample;
        }
    }

    public function toArray(): array
    {
        $ret =  [
            'id'             => $this->getId(),
            'idSession'      => $this->getSession()->getId(),
            'idUser'         => $this->getIdUser(),
            'isApproved'     => $this->getIsApproved(),
            'cashout'        => $this->getCashout(),
            'startTime'      => $this->getStart(),
            'endTime'        => $this->getEnd(),
            'cashin'         => $this->getCashin(),
            'totalCredit'    => $this->getTotalCredit(),
            'totalCash'      => $this->getTotalCash(),
            'points'         => (float)$this->getAccumulatedPoints(),
            'minimumMinutes' => (int)$this->getMinimumMinutes()
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
