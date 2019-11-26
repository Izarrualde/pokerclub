<?php
namespace Solcre\Pokerclub\Service;

use Doctrine\ORM\ORMException;
use Solcre\Pokerclub\Entity\DealerTipSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\DealerTipExceptions;
use Solcre\Pokerclub\Exception\SessionExceptions;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;
use Solcre\Pokerclub\Entity\SessionEntity;

class DealerTipSessionService extends BaseService
{

    public const STATUS_CODE_404 = 404;
    public const AVATAR_FILE_KEY = 'avatar_file';

    private $config;

    public function __construct(EntityManager $entityManager, array $config)
    {
        parent::__construct($entityManager);
        $this->config = $config;
    }
    
    public function checkGenericInputData($data): void
    {
        // does not include id
        if (! isset($data['idSession'], $data['hour'], $data['dealerTip'])) {
            throw BaseException::incompleteDataException();
        }

        if (!is_numeric($data['dealerTip']) || $data['dealerTip'] < 0) {
            throw DealerTipExceptions::dealerTipInvalidException();
        }
    }

    public function add($data)
    {
        $this->checkGenericInputData($data);

        $dealerTip    = new DealerTipSessionEntity();

        try {
            $session = $this->entityManager->getReference(SessionEntity::class, $data['idSession']);
        } catch (ORMException $e) {
            throw SessionExceptions::sessionNotFoundException();
        }

        $dealerTip->setSession($session);
        $dealerTip->setDealerTip($data['dealerTip']);
        $dealerTip->setHour(new \DateTime($data['hour']));

        $this->entityManager->persist($dealerTip);
        $this->entityManager->flush($dealerTip);

        return $dealerTip;
    }

    public function update($id, $data)
    {
        $this->checkGenericInputData($data);

        if (!isset($data['id'])) {
            throw BaseException::incompleteDataException();
        }

        try {
            $dealerTip    = $this->fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw DealerTipExceptions::dealerTipNotFoundException();
            }

            throw $e;
        }
        
        $dealerTip->setHour(new \DateTime($data['hour']));
        $dealerTip->setDealerTip($data['dealerTip']);

        $this->entityManager->flush($dealerTip);

        return $dealerTip;
    }

    public function delete($id, $entityObj = null): bool
    {
        try {
            $dealerTip = $this->fetch($id);

            $this->entityManager->remove($dealerTip);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw DealerTipExceptions::dealerTipNotFoundException();
            }

            throw $e;
        }
    }
}
