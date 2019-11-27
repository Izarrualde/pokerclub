<?php
namespace Solcre\Pokerclub\Service;

use Solcre\Pokerclub\Entity\BuyinSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\BuyinExceptions;
use Solcre\Pokerclub\Exception\UserSessionExceptions;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;

class BuyinSessionService extends BaseService
{
    public const STATUS_CODE_404 = 404;
    public const AVATAR_FILE_KEY = 'avatar_file';

    private $config;
    protected $userSessionService;

    public function __construct(
        EntityManager $em,
        userSessionService $userSessionService,
        array $config
    ) {
        parent::__construct($em);
        $this->userSessionService = $userSessionService;
        $this->config             = $config;
    }

    public function fetchAllBuyins($sessionId)
    {
        return $this->repository->fetchAll($sessionId);
    }

    public function checkGenericInputData($data): void
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
            throw BaseException::incompleteDataException();
        }

        if (!is_numeric($data['amountCash']) ||
            !is_numeric($data['amountCredit']) ||
            ($data['amountCash'] < 0) ||
            ($data['amountCredit'] < 0)) {
            throw BuyinExceptions::buyinInvalidException();
        }
    }

    public function add($data)
    {
        $this->checkGenericInputData($data);

        $buyin = new BuyinSessionEntity();
        $buyin->setHour(new \DateTime($data['hour']));
        $buyin->setAmountCash($data['amountCash']);
        $buyin->setAmountCredit($data['amountCredit']);
        $buyin->setCurrency($data['currency']);
        $buyin->setIsApproved($data['approved']);

        try {
            $userSession = $this->userSessionService->fetch($data['idUserSession']);
        } catch (Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw UserSessionExceptions::userSessionNotFoundException();
            }

            throw $e;
        }

        if (! $userSession instanceof UserSessionEntity) {
            throw UserSessionExceptions::userSessionNotFoundException();
        }

        if ($userSession->getBuyins()->isEmpty()) {
            $userSession->setStart(new \DateTime($data['hour']));
        }
        
        $buyin->setUserSession($userSession);

        $this->entityManager->persist($buyin);
        $this->entityManager->flush();
        
        return $buyin;
    }

    public function update($id, $data)
    {
        $this->checkGenericInputData($data);

        if (!isset($data['id'])) {
            throw BaseException::incompleteDataException();
        }

        try {
            $buyin = $this->fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw BuyinExceptions::buyinNotFoundException();
            }

            throw $e;
        }

        if (! $buyin instanceof BuyinSessionEntity) {
            throw BuyinExceptions::buyinNotFoundException();
        }

        $buyin->setHour(new \DateTime($data['hour']));
        $buyin->setAmountCash($data['amountCash']);
        $buyin->setAmountCredit($data['amountCredit']);

        $this->entityManager->flush($buyin);

        return $buyin;
    }

    public function delete($id, $entityObj = null): bool
    {
        try {
            $buyin = $this->fetch($id);

            $this->entityManager->remove($buyin);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw BuyinExceptions::buyinNotFoundException();
            }

            throw $e;
        }
    }
}
