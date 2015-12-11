<?php namespace LynxGroup\Component\Authenticating;

use LynxGroup\Component\Odm\Document;

class Role extends Document
{
	public function setName($name)
	{
		$this->data['name'] = $name;

		return $this->setDirty();
	}

	public function getName()
	{
		return isset($this->data['name']) ? $this->data['name'] : null;
	}

	public function addRole($role)
	{
		$this->data['roles'][] = $role;

		return $this->setDirty();
	}

	public function getRoles()
	{
		return isset($this->data['roles']) ? $this->data['roles'] : [];
	}

	public function getTreeRoles()
	{
		$roles = [];

		foreach( $this->getRoles() as $role )
		{
			$roles[] = $role;

			$child = $this->odm->query()->kindof('Document\\Role')->where('name', $role)->find();

			if( $child )
			{
				$roles = array_merge($roles, $child->getTreeRoles());
			}
		}

		return $roles;
	}
}
