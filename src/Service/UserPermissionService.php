<?php

namespace Solcre\Pokerclub\Service;

use Solcre\SolcreFramework2\Service\BaseService;
use Solcre\Pokerclub\Entity\UserPermissionEntity;

class UserPermissionService extends BaseService
{
    public function getPermissionsByUser($user): array
    {
        $userPermissions  = $this->fetchAll(['user' => $user]);
        $arrayPermissions = [];

        foreach ($userPermissions as $permission) {
            $arrayPermissions[] = [
                'permission_id' => $permission->getPermission(),
                'r'             => $permission->getRead(),
                'w'             => $permission->getWrite(),
                'd'             => $permission->getDelete()
            ];
        }

        $userPermissions = [
            'user_id'     => $user,
            'permissions' => $arrayPermissions
        ];

        return $userPermissions;
    }

    public function update($id, $data)
    {
        foreach ($data['permissions'] as $permission) {
            $userPermission = $this->fetchBy(
                [
                    'permission' => $permission['permission_id'],
                    'user'       => $id
                ]
            );

            // if is already an user group with the permission update.
            if ($userPermission instanceof UserPermissionEntity) {
                $userPermission->setDelete($permission['d']);
                $userPermission->setRead($permission['r']);
                $userPermission->setWrite($permission['w']);
            } else {
                // add new association user group and permission.
                $userPermissionData = [
                    'user'       => $id,
                    'permission' => $permission['permission_id'],
                    'delete'     => $permission['d'],
                    'write'      => $permission['w'],
                    'read'       => $permission['r'],
                ];

                $this->add($userPermissionData);
            }
        }

        //update all the user groups and permissions options
        $this->entityManager->flush();
        $userPermissions = [
            'user_id'     => $data['user_id'],
            'permissions' => $data['permissions']
        ];

        return $userPermissions;
    }
}
