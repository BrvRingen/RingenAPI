<?php
namespace Api\Database;

class BasicAuthUsers
{
	public function CheckLogonUser($user, $pwd)
	{
		$usersList =
		[
			'User1' => 'Pwd1',
			'User2' => 'Pwd2',
			'User3' => 'Pwd3'
		];
	
		return $usersList[$user] == $pwd;
	}
}