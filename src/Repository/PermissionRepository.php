<?php

namespace Solcre\Pokerclub\Repository;

use Solcre\SolcreFramework2\Common\BaseRepository;

class PermissionRepository extends BaseRepository
{
    public function getPermissionsByUser($cellphone)
    {
        $conn = $this->_em->getConnection();
        $sql = '(SELECT `id`, 
        `id_permiso`, 
        `w`, 
        `d`, 
        `r`, 
        (SELECT `nombre` 
         FROM   `permisos` 
         WHERE  `id` = `permisos_usuarios`.`id_permiso`) AS `nombre` 
		 FROM   `permisos_usuarios` 
 		 WHERE  `permisos_usuarios`.`id_usuario` IN (SELECT `id` 
                                         FROM   `users` 
                                         WHERE  `username` = :cellphone)) 
		UNION 
		(SELECT `id`, 
        `id_permiso`, 
        `w`, 
        `d`, 
        `r`, 
        (SELECT `nombre` 
         FROM   `permisos` 
         WHERE  `id` = `permisos_grupos`.`id_permiso`) AS `nombre` 
 		FROM   `permisos_grupos` 
 		WHERE  `permisos_grupos`.`id_grupo` IN (SELECT `id_grupo` 
                                                     FROM   `usuarios_pertenece` 
                                                     WHERE 
                `id_usuario` IN (SELECT `id` 
                              FROM   `users` 
                              WHERE  `username` 
                             = :cellphone)))';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':cellphone', $cellphone);
        $stmt->execute();
        
        return $stmt->fetchAll();

    }

    public function checkPermissionExists($name, $id = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p');
        $qb->from($this->_entityName, 'p');
        $qb->where('p.name = :name');
        if (!empty($id)) {
            $qb->andWhere('p.id != :id');
            $qb->setParameter('id', $id);
        }
        $qb->setParameter('name', $name);
        $query = $qb->getQuery();
        $query->execute();

        return $query->getOneOrNullResult();
    }
}
