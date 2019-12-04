<?php
namespace Solcre\Pokerclub\Service;

use InvalidArgumentException;
use Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\UserExceptions;
use Solcre\SolcreFramework2\Service\BaseService;
use Solcre\SolcreFramework2\Utility\Validators;
use Exception;

class UserService extends BaseService
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
        if (!isset(
            $data['password'],
            $data['name'],
            $data['lastname'],
            $data['email'],
            $data['username'],
            $data['multiplier'],
            $data['active'],
            $data['hours'],
            $data['points'],
            $data['sessions'],
            $data['results'],
            $data['cashin']
        )
        ) {
            throw BaseException::incompleteDataException();
        }

        if (! is_numeric($data['multiplier']) ||
            (! is_numeric($data['hours']) || $data['hours'] < 0) ||
            (! is_numeric($data['points']) || $data['points'] < 0) ||
            (! is_numeric($data['sessions']) || $data['sessions'] < 0) ||
            (! is_numeric($data['results'])) ||
            (! is_numeric($data['cashin']) || $data['cashin'] < 0)) {
            throw UserExceptions::userInvalidException();
        }
    }

    private function validateCellphone($cellphone): void
    {
        // Check the lenght of number
        if (strlen($cellphone) !== 9) {
            throw new InvalidArgumentException('El número celular debe tener 9 dígitos', 422);
        }

        // Check that contains only numeric values
        if (! preg_match('/^\d*$/', $cellphone)) {
            throw new InvalidArgumentException('El número celular debe contener sólo dígitos', 422);
        }

        $cellphoneArray = str_split($cellphone);

        // Check that first digit is equal to 0
        if ((int)$cellphoneArray[0] !== 0) {
            throw new InvalidArgumentException('El primer dígito del número celular debe ser 0', 422);
        }

        // Check that second digit is equal to 9
        if ((int)$cellphoneArray[1] !== 9) {
            throw new InvalidArgumentException('El segundo dígito del número celular debe ser 9', 422);
        }

        // Check that third digit is distinct to 0
        if ((int)$cellphoneArray[2] === 0) {
            throw new InvalidArgumentException('El tercer dígito del número celular debe ser distinto de 0', 422);
        }
    }

    public function add($data)
    {
        $this->checkGenericInputData($data);

        $this->validateCellphone($data['username']);

        if (! Validators::valid_email($data['email'])) {
            throw UserExceptions::invalidEmailException();
        }

        if ($this->userExists($data)) {
            throw UserExceptions::userAlreadyExistException();
        }

        $user = new UserEntity();
        $user->setPassword($data['password']);
        $user->setName($data['name']);
        $user->setLastname($data['lastname']);
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setMultiplier($data['multiplier']);
        $user->setIsActive($data['active']);
        $user->setHours($data['hours']);
        $user->setPoints($data['points']);
        $user->setSessions($data['sessions']);
        $user->setResults($data['results']);
        $user->setCashin($data['cashin']);

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);

        return $user;
    }

    public function update($id, $data)
    {
        $this->checkGenericInputData($data);

        if (! isset($data['id']/*, $data['password_confirm'], $data['avatar_file'], $data['logged_user_username']*/)) {
            throw BaseException::incompleteDataException();
        }

        try {
            $user = $this->fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw UserExceptions::userNotFoundException();
            }

            throw $e;
        }

        if (! $user instanceof UserEntity) {
            throw UserExceptions::userNotFoundException();
        }

        /*
        $loggedUser = $this->fetchBy(
            [
                'username' => $data['logged_user_username']
            ]
        );

        if (! $loggedUser instanceof UserEntity || $loggedUser->getUsername() !== $user->getUsername()) {
            throw new Exception('Method not allowed for current user', 400);
        }
        */

        /*
        if ($this->userExists($data, $id)) {
            throw UserExceptions::userAlreadyExistException();
        }
        */

        if (! Validators::valid_email($data['email'])) {
            throw UserExceptions::invalidEmailException();
        }

        /*
        if ($data['avatar_file'] !== null) {
            $this->deleteAvatarFromServer($user);
            $avatar = $this->uploadAvatarToServer($data, true);
            $user->setAvatarVisibleFilename($avatar['name']);
            $user->setAvatarHashedFilename(File::fileNameExtract($avatar['tmp_name']));
        }
        elseif ($data['avatar_visible_filename'] === null) {
            $this->deleteAvatarFromServer($user);
            $user->setAvatarVisibleFilename(null);
            $user->setAvatarHashedFilename(null);
        }
        */

        /*
        if ($data['password'] !== null) {
            $password = $this->checkPasswords($data['password'], $data['password_confirm']);
            $user->setPassword($this->hashPassword($password));
        }
        */

        $user->setName($data['name']);
        $user->setLastname($data['lastname']);
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setMultiplier($data['multiplier']);
        $user->setPassword($data['password']);
        $user->setIsActive($data['active']);
        $user->setSessions($data['sessions']);
        $user->setHours($data['hours']);
        $user->setResults($data['results']);
        $user->setCashin($data['cashin']);

        $this->entityManager->flush($user);

        return $user;
    }

    public function delete($id, $entityObj = null): bool
    {
        try {
            $user = $this->fetch($id);

            $this->entityManager->remove($user);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw UserExceptions::userNotFoundException();
            }

            throw $e;
        }
    }

    private function userExists($data): bool
    {
        return $this->repository->userExists($data);
    }

    private function checkPasswords($password, $passwordConfirm)
    {
        if ($password !== $passwordConfirm) {
            throw new Exception('Passwords do not match', 400);
        }

        return $password;
    }

    /*
    private function hashPassword($password)
    {
        return Strings::bcryptPassword($password);
    }
    */

    /*
    private function uploadAvatarToServer(array $data, $isUploaded = false)
    {
        $name = $data[self::AVATAR_FILE_KEY]['name'];
        $file = File::uploadFile(
            $data,
            [
                'is_uploaded' => $isUploaded,
                'target_dir'  => $this->getPathOfAvatars(),
                'key'         => self::AVATAR_FILE_KEY,
                'hash'        => true
            ]);

        $file['name'] = $name;

        return $file;
    }
    */

    /*
    private function deleteAvatarFromServer(UserEntity $user)
    {
        if ($user->getAvatarHashedFilename() !== null) {
            $fullPathOfAvatar = $this->getFullPathOfAvatar($user->getAvatarHashedFilename());
            if (file_exists($fullPathOfAvatar)) {
                unlink($fullPathOfAvatar);
            }
        }
    }
    */

    public function getFullPathOfAvatar($avatar): string
    {
        return $this->getPathOfAvatars() . $avatar;
    }

    private function getPathOfAvatars()
    {
        return $this->config['lms']['PATHS']['avatars'];
    }
}
