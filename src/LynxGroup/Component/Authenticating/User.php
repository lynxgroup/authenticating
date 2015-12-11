<?php namespace LynxGroup\Component\Authenticating;

use LynxGroup\Component\Odm\Document;

class User extends Document
{
	public function setUsername($username)
	{
		$this->data['username'] = $username;

		return $this->setDirty();
	}

	public function getUsername()
	{
		return isset($this->data['username']) ? $this->data['username'] : null;
	}

	public function setEmail($email)
	{
		$this->data['email'] = $email;

		return $this->setDirty();
	}

	public function getEmail()
	{
		return isset($this->data['email']) ? $this->data['email'] : null;
	}

	public function setPassword($password)
	{
		$data['salt'] = '$'.'6'.'$rounds='.'5042'.'$'.uniqid('', true);

		$data['hash'] = crypt($password, $data['salt']);

		$this->data['password'] = $data;

		return $this->setDirty();
	}

	public function getPassword()
	{
		return isset($this->data['password']) ? $this->data['password'] : [];
	}

	public function cmpPassword($password)
	{
		$data = $this->getPassword();

		return
			isset($data['salt']) &&
			isset($data['hash']) &&
			crypt($password, $data['salt']) == $data['hash']
		;
	}

	public function setClave($clave)
	{
		$this->data['clave'] = $clave;

		$this->data['clave-updated'] = date('Y/m/d H:i:s');

		return $this->setDirty();
	}

	public function getClave()
	{
		return isset($this->data['clave']) ? $this->data['clave'] : null;
	}

	public function getClaveUpdated()
	{
		return isset($this->data['clave-updated']) ? $this->data['clave-updated'] : null;
	}

	public function getClaveUpdatedHours()
	{
		return round( abs(strtotime($this->getClaveUpdated()) - strtotime(date('Y/m/d H:i:s'))) / ( 86400 / 24 ) );
	}

	public function makeClave()
	{
		return $this->setClave('$'.'6'.'$rounds='.'5042'.'$'.uniqid('', true));
	}

	public function setNicknames(array $nicknames)
	{
		$this->data['nickname'] = $nicknames;

		return $this->setDirty();
	}

	public function setNickname($locale, $nickname)
	{
		$this->data['nickname'][$locale] = $nickname;

		return $this->setDirty();
	}

	public function getNickname($locale)
	{
		return isset($this->data['nickname'][$locale]) ? $this->data['nickname'][$locale] : null;
	}

	public function setEnabled($enabled)
	{
		$this->data['enabled'] = $enabled;

		return $this->setDirty();
	}

	public function getEnabled()
	{
		return isset($this->data['enabled']) ? $this->data['enabled'] : false;
	}

	public function setLocked($locked)
	{
		$this->data['locked'] = $locked;

		return $this->setDirty();
	}

	public function getLocked()
	{
		return isset($this->data['locked']) ? $this->data['locked'] : false;
	}

	public function addRole($role)
	{
		if( !isset($this->data['roles']) )
		{
			$this->data['roles'] = [];
		}

		if( !in_array($role, $this->data['roles']) )
		{
			$this->data['roles'][] = $role;
		}

		return $this;
	}

	public function hasRole($role)
	{
		return in_array($role, $this->data['roles']);
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

