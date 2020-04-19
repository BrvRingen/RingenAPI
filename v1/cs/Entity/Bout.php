<?php
namespace Api\Entity;

class Bout extends BaseEntity
{
	public	
		$saisonId,
		$competitionId,
		$order,
		$weightClass,
		$style,
		$homeWrestlerId,
		$homeWrestlerLicId,
		$homeWrestlerName,
		$homeWrestlerGivenname,
		$opponentWrestlerId,
		$opponentWrestlerLicId,
		$opponentWrestlerName,
		$opponentWrestlerGivenname,
		$homeWrestlerPoints,
		$homeWrestlerFlags,
		$opponentWrestlerPoints,
		$opponentWrestlerFlags,
		$result,
		$round1,
		$round2,
		$round3,
		$round4,
		$round5,
		$homeWrestlerStatus,
		$opponentWrestlerStatus;
}