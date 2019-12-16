<?php

namespace Solcre\Pokerclub\Service;

use Solcre\SolcreFramework2\Service\BaseService;
use Solcre\Pokerclub\Entity\UserGroupPermissionEntity;
use ArrayObject;

class UserGroupPermissionService extends BaseService
{

    public function getPermissionsByGroup($userGroup): ArrayObject
    {
        $userGroupPermissions = $this->fetchAll(['userGroup' => $userGroup]);
        $arrayPermissions = [];
        foreach ($userGroupPermissions as $permission) {
            $arrayPermissions[] = [
                'permission_id' => $permission->getPermission(),
                'r'             => $permission->getRead(),
                'w'             => $permission->getWrite(),
                'd'             => $permission->getDelete()
            ];
        }

        $userGroupPermissions = [
            'group_id'    => $userGroup,
            'permissions' => $arrayPermissions
        ];

        return new ArrayObject($userGroupPermissions);
    }

    public function update($id, $data)
    {
        foreach ($data['permissions'] as $permission) {
            $userGroupPermission = $this->fetchBy(
                [
                    'permission' => $permission['permission_id'],
                    'userGroup'  => $id
                ]
            );

            // if is already an user group with the permission update.
            if ($userGroupPermission instanceof UserGroupPermissionEntity) {
                $userGroupPermission->setDelete($permission['d']);
                $userGroupPermission->setRead($permission['r']);
                $userGroupPermission->setWrite($permission['w']);
            } else {
                // add new association user group and permission.
                $userGroupPermissionData = [
                    'userGroup'  => $id,
                    'permission' => $permission['permission_id'],
                    'delete'     => $permission['d'],
                    'write'      => $permission['w'],
                    'read'       => $permission['r'],
                ];

                $this->add($userGroupPermissionData);
            }
        }

        // Uupdate all the user groups and permissions options
        $this->entityManager->flush();
        $userGroupPermissions = [
            'group_id'    => $data['group_id'],
            'permissions' => $data['permissions']
        ];

        return $userGroupPermissions;
    }
}
