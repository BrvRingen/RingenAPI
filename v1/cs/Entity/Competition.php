<?php
namespace Api\Entity;

class Competition extends BaseEntity
{
	public	
	$saisonId,
	$competitionId,
	$ligaId,
	$tableId,
	$status,
	$scheme,
	$manualInput,
	$inTable,
	$inStatistics,
	$invalidated,
	$planned,
	$boutday,
	$homeTeamName,
	$opponentTeamName,
	$homePoints,
	$opponentPoints,
	$boutDate,
	$scaleTime,
	$audience,
	$location,
	$editorName,
	$editorComment,
	$refereeName,
	$refereeGivenname,
	$lastModified,
	$editedAt,
	$editedBy,
	$editedIpAddr,
	$controlledAt,
	$controlledBy,
	$controllerComment,
	$validatedHomePoints,
	$validatedOpponentPoints,
	$decision;
}