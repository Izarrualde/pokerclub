<?php
namespace Solcre\Pokerclub\Service;

use Solcre\SolcreFramework2\Service\BaseService;

class UserGroupService extends BaseService
{

    public function add($data)
    {
        $data['name'] = strtolower($data['name']);
        $groupExists  = $this->fetchBy(
            array('name' => $data['name'])
        );

        /* Check if group name is already taken. */
        if (count($groupExists) > 0) {
            throw new \RuntimeException('User group name already taken.', 409);
        }

        return parent::add($data);
    }

    public function update($id, $data)
    {
        $userGroup = $this->fetch($id);
        $userGroup->setName($data['name']);
        $this->entityManager->flush();

        return $userGroup;
    }
}
