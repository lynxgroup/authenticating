<?php namespace LynxGroup\Component\Authenticating;

use LynxGroup\Contracts\Authenticating\Authenticator as AuthenticatorInterface;

class Authenticator implements AuthenticatorInterface
{
	protected $odm;

	protected $session;

	protected $cookies;

	protected $user;

	public function __construct(
		\Odm\Odm $odm,
		\Http\Session $session,
		\Http\Cookies $cookies
	)
	{
		$this->odm = $odm;

		$this->session = $session;

		$this->cookies = $cookies;
	}

	public function setReturnUrl($url)
	{
		$this->session->set('security-return-url', $url);
	}

	public function login(\Odm\Document $user, $remember = false)
	{
		$this->session->set('security-user', $user->getId());
		$this->session->set('security-password', $user->getPassword());

		if( $remember )
		{
			$this->cookies->set('security-user', $user->getId());
			$this->cookies->set('security-password', $user->getPassword());
		}

		$this->session->clear('security-return-url');
	}

	public function logout()
	{
		$this->session->clear('security-user');
		$this->session->clear('security-password');

		$this->cookies->clear('security-user');
		$this->cookies->clear('security-password');

		$this->user = null;
	}

	public function getUser()
	{
		if( !$this->user )
		{
			if( $this->cookies->exists('security-user') && $this->cookies->exists('security-password') )
			{
				$user = $this->odm->load($this->cookies->get('security-user'));

				if( $user && $user->getPassword() === $this->cookies->get('security-password') )
				{
					$this->user = $user;
				}
			}
			else if( $this->session->exists('security-user') && $this->session->exists('security-password') )
			{
				$user = $this->odm->load($this->session->get('security-user'));

				if( $user && $user->getPassword() === $this->session->get('security-password') )
				{
					$this->user = $user;
				}
			}
		}

		return $this->user;
	}
}
