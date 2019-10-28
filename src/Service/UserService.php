<?php
namespace Solcre\Pokerclub\Service;

use Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\UserHadActionException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Solcre\Pokerclub\Exception\UserNotFoundException;
use Solcre\Pokerclub\Exception\UserInvalidException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;

class UserService extends BaseService
{
    
    const STATUS_CODE_404 = 404;
    const AVATAR_FILE_KEY = 'avatar_file';

    private $config;

    public function __construct(EntityManager $entityManager, array $config)
    {
        parent::__construct($entityManager);
        $this->config = $config;
    }


    public function checkGenericInputData($data)
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
            throw new IncompleteDataException();
        }

        if (!is_numeric($data['multiplier']) ||
            (!is_numeric($data['hours']) || $data['hours'] < 0) ||
            (!is_numeric($data['points']) || $data['points'] < 0) ||
            (!is_numeric($data['sessions']) || $data['sessions'] < 0)||
            !is_numeric($data['results']) ||
            (!is_numeric($data['cashin']) || $data['cashin'] < 0)) {
            throw new UserInvalidException();
        }
    }

    public function add($data)
    {
        $this->checkGenericInputData($data);

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

        if (!isset($data['id'])) {
            throw new IncompleteDataException();
        }

        try {
            $user = parent::fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new UserNotFoundException();
            }
            throw $e;
        }

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
            $user = parent::fetch($id);

            $this->entityManager->remove($user);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new UserNotFoundException();
            }
            throw $e;
        }
    }
    /*
    public function update($id, $data)
    {
        $user = $this->fetchBy(
            [
                'id' => $id
            ]);
        if (! $user instanceof UserEntity)
        {
            throw new Exception('User not found', 404);
        }
        $loggedUser = $this->fetchBy(
            [
                'cellphone' => $data['logged_user_cellphone']
            ]);
        if (! $loggedUser instanceof UserEntity || $loggedUser->getCellphone() !== $user->getCellphone())
        {
            throw new Exception('Method not allowed for current user', 400);
        }
        if ($this->userExists($data, $id))
        {
            throw new Exception('User already exists', 400);
        }
        if (! Validators::valid_email($data['email']))
        {
            throw new Exception('Invalid email', 400);
        }
        if ($data['avatar_file'] !== null)
        {
            $this->deleteAvatarFromServer($user);
            $avatar = $this->uploadAvatarToServer($data, true);
            $user->setAvatarVisibleFilename($avatar['name']);
            $user->setAvatarHashedFilename(File::fileNameExtract($avatar['tmp_name']));
        }
        elseif ($data['avatar_visible_filename'] === null)
        {
            $this->deleteAvatarFromServer($user);
            $user->setAvatarVisibleFilename(null);
            $user->setAvatarHashedFilename(null);
        }
        if ($data['password'] !== null)
        {
            $password = $this->checkPasswords($data['password'], $data['password_confirm']);
            $user->setPassword($this->hashPassword($password));
        }
        $user->setCellphone($data['cellphone']);
        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setLastName($data['last_name']);
        $this->entityManager->flush();
        return $user;
    }

    private function userExists($data, $id): bool
    {
        return $this->repository->userExists($data, $id);
    }

    private function checkPasswords($password, $passwordConfirm)
    {
        if ($password !== $passwordConfirm)
        {
            throw new Exception('Passwords do not match', 400);
        }
        return $password;
    }

    private function hashPassword($password)
    {
        return Strings::bcryptPassword($password);
    }

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

    private function deleteAvatarFromServer(UserEntity $user)
    {
        if ($user->getAvatarHashedFilename() !== null)
        {
            $fullPathOfAvatar = $this->getFullPathOfAvatar($user->getAvatarHashedFilename());
            if (file_exists($fullPathOfAvatar))
            {
                unlink($fullPathOfAvatar);
            }
        }
    }

    public function getFullPathOfAvatar($avatar): string
    {
        return $this->getPathOfAvatars() . $avatar;
    }

    private function getPathOfAvatars()
    {
        return $this->config['lms']['PATHS']['avatars'];
    }
    */
}
