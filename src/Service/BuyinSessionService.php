<?php
namespace Solcre\Pokerclub\Service;

use Solcre\Pokerclub\Entity\BuyinSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\BuyinInvalidException;
use Solcre\Pokerclub\Exception\BuyinNotFoundException;
use Solcre\Pokerclub\Exception\UserSessionNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Exception;

class BuyinSessionService extends BaseService
{
    const STATUS_CODE_404 = 404;

    protected $userSessionService;

    public function __construct(
        EntityManager $em,
        userSessionService $userSessionService
    ) {
        parent::__construct($em);
        $this->userSessionService = $userSessionService;
    }

    public function fetchAllBuyins($sessionId)
    {
        return $this->repository->fetchAll($sessionId);
    }

    public function checkGenericInputData($data)
    {
        // does not include id

        if (!isset(
            $data['hour'],
            $data['amountCash'],
            $data['amountCredit'],
            $data['currency'],
            $data['approved'],
            $data['idUserSession']
        )
        ) {
            throw new IncompleteDataException();
        }

        if (!is_numeric($data['amountCash']) ||
            !is_numeric($data['amountCredit']) ||
            ($data['amountCash'] < 0) ||
            ($data['amountCredit'] < 0)) {
            throw new BuyinInvalidException();
        }
    }

    public function add($data, $strategies = null)
    {
        $this->checkGenericInputData($data);

        $buyin        = new BuyinSessionEntity();
        $buyin->setHour(new \DateTime($data['hour']));
        $buyin->setAmountCash($data['amountCash']);
        $buyin->setAmountCredit($data['amountCredit']);
        $buyin->setCurrency($data['currency']);
        $buyin->setIsApproved($data['approved']);

        try {
            $userSession = $this->userSessionService->fetch($data['idUserSession']);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new UserSessionNotFoundException();
            }
            throw $e;
        }
        
        if ($userSession->getBuyins()->isEmpty()) {
            $userSession->setStart($data['hour']);
            $this->entityManager->persist($userSession);
        }
        
        $buyin->setUserSession($userSession);

        $this->entityManager->persist($buyin);
        $this->entityManager->flush();

        return $buyin;
    }

    public function update($data, $strategies = null)
    {
        $this->checkGenericInputData($data);

        if (!isset($data['id'])) {
            throw new IncompleteDataException();
        }

        try {
            $buyin = parent::fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new BuyinNotFoundException();
            }
            throw $e;
        }

        $buyin->setHour(new \DateTime($data['hour']));
        $buyin->setAmountCash($data['amountCash']);
        $buyin->setAmountCredit($data['amountCredit']);

        $this->entityManager->flush($buyin);

        return $buyin;
    }

    public function delete($id, $entityObj = null)
    {
        try {
            $buyin    = parent::fetch($id);

            $this->entityManager->remove($buyin);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new BuyinNotFoundException();
            }
            throw $e;
        }
    }
}
