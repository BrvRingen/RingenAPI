<?php
namespace Api\Controller;

use Api\Library\ApiException;
use Api\Entity\liga;
use Api\Entity\Bout;
use Api\Entity\BoutAnnotation;
use Api\Entity\saison;
use Api\Entity\competition;
use Api\Entity\wrestler;
use Api\Database\DatabaseConnection;
use Api\Database\BasicAuthUsers;

/**
 * Class MkController
 * Endpunkt für die Mannschaftdskampf-Funktionen.
 *
 * @package Api\Controller
 */
class CsController extends ApiController
{
	public function indexAction()
	{
		try {
			// Checks auslösen
			$this->initialize();

			$input = json_decode(file_get_contents('php://input'), true);

			if ($_SERVER['REQUEST_METHOD'] == 'GET')
			{
				$op = $_GET['op'];
				
				if($op == 'listSaison')
					$result = $this->GetListSaison();
				elseif($op == 'listLiga')
					$result = $this->GetListLiga($_GET['sid']);
				elseif($op == 'listCompetition')
					$result = $this->GetListCompetition($_GET['sid'], $_GET['ligaId'], $_GET['rid']);
				elseif($op == 'getCompetition')
					$result = $this->GetCompetition($_GET['sid'], $_GET['cid']);
				elseif($op == 'getSaisonWrestler')
					$result = $this->GetSaisonWrestler($_GET['passcode']);
				else
					throw new ApiException('', ApiException::UNKNOWN_METHOD);
			}
			//elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
			//	$result = $this->create($input);
			//}
			elseif ($_SERVER['REQUEST_METHOD'] == 'PUT')
			{
				//if(isset($_GET['saisonId']) && isset($_GET['competitionId']))
				//	$result = $this->UpdateCompetition($input, $_GET['saisonId'], $_GET['competitionId']);
			}
			//elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
			//	$result = $this->delete((int)$_GET['id']);
			//}
			else {
				throw new ApiException('', ApiException::UNKNOWN_METHOD);
			}

			header('HTTP/1.0 200 OK');
		} catch (ApiException $e) {
			if ($e->getCode() == ApiException::AUTHENTICATION_FAILED)
				header('HTTP/1.0 401 Unauthorized');
			elseif ($e->getCode() == ApiException::MALFORMED_INPUT)
				header('HTTP/1.0 400 Bad Request');
			elseif ($e->getCode() == ApiException::UNKNOWN_METHOD)
				header('HTTP/1.0 400 Bad Request');
			elseif ($e->getCode() == ApiException::UNKNOWN_ID)
				header('HTTP/1.0 400 Bad Request');
			elseif ($e->getCode() == ApiException::NOT_IMPLEMENTED)
				header('HTTP/1.0 400 Bad Request');
				
			$result = ['message' => $e->getMessage()];
		}
		
		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode($result); //JSON_PARTIAL_OUTPUT_ON_ERROR
	}


	private function GetListSaison()
	{
		$databaseConnection = new DatabaseConnection();
		$conn = $databaseConnection->Connect();		
		$dbResults = $conn->query("SELECT * FROM jos_rdb_cs");
		
		$saisonList = [];
		while ($saison = $dbResults->fetch_object('Api\Entity\saison')) {
			$saisonList[$saison->saisonId] = ($saison)->toArray();
		}
		
		$databaseConnection->Close($conn);

		return array('rpcid' => null, 'rc' => 'ok', 'api' => array('rdb' => '3.0.8 \/ 3.0.9','jrcs' => '1.0.3'), 'saisonList' => $saisonList);
	}
	
	private function GetListLiga($sid)
	{
		$databaseConnection = new DatabaseConnection();
		$conn = $databaseConnection->Connect();		
		$dbResults = $conn->query("SELECT * FROM jos_rdb_cs__table WHERE saisonId='".$sid."'");

		$ligaList = [];
		$i = 1;
		while ($liga = $dbResults->fetch_object('Api\Entity\liga'))
		{
			if($ligaList[utf8_encode($liga->ligaId)][utf8_encode($liga->tableId)] == null) //[$liga->tableId]
			{
				$ligaList[utf8_encode($liga->ligaId)][utf8_encode($liga->tableId)] = $liga->toArray(); //[$liga->tableId]				
			}
			else {
				array_push($ligaList[utf8_encode($liga->ligaId)][utf8_encode($liga->tableId)], $liga->toArray()); //[$liga->tableId]
			}
		}
		
		$databaseConnection->Close($conn);

		return array('rpcid' => null, 'rc' => 'ok', 'api' => array('rdb' => '3.0.8 \/ 3.0.9','jrcs' => '1.0.3'), 'year' => '2021','sid' => $sid, 'ligaList' => $ligaList);
	}	
	
	private function GetListCompetition($sid, $ligaId, $rid)
	{
		$databaseConnection = new DatabaseConnection();
		$conn = $databaseConnection->Connect();		

		$dbResults = $conn->query("SELECT * FROM jos_rdb_cs__competition WHERE saisonId='".$sid."' AND ligaId='".$ligaId."' AND tableId='".$rid."'");
		$competitionList = [];
		while ($competition = $dbResults->fetch_object('Api\Entity\competition')) {
			$competitionList[$competition->competitionId] = ($competition)->toArray();
		}
		
		$databaseConnection->Close($conn);

		return array('rpcid' => null, 'rc' => 'ok', 'api' => array('rdb' => '3.0.8 \/ 3.0.9','jrcs' => '1.0.3'), 'year' => '2021','sid' => $sid,'lid' => $ligaId,'rid' => $rid, 'competitionList' => $competitionList);
		
	}

	private function GetCompetition($sid, $cid)
	{
		$databaseConnection = new DatabaseConnection();
		$conn = $databaseConnection->Connect();		

		$competition = [];
		
		$dbResults = $conn->query("SELECT * FROM jos_rdb_cs__competition WHERE saisonId='".$sid."' AND competitionId='".$cid."'");
		$competition = $dbResults->fetch_object('Api\Entity\competition')->toArray();


		$dbResults = $conn->query("SELECT * FROM jos_rdb_cs__bout WHERE saisonId='".$sid."' AND competitionId='".$cid."'");

		$Bouts = [];
		while ($Bout = $dbResults->fetch_object('Api\Entity\Bout'))
		{
			$BoutArray = $Bout->toArray();
			$dbBoutAnnotationsResults = $conn->query("SELECT * FROM jos_rdb_cs__bout__annotation WHERE saisonId='".$sid."' AND competitionId='".$cid."' AND `order`=".$Bout->order);

			$BoutAnnotations = [];
			while ($BoutAnnotation = $dbBoutAnnotationsResults->fetch_object('Api\Entity\BoutAnnotation')) {
				$BoutArray["annotation"]["1"][$BoutAnnotation->type] = $BoutAnnotation->toArray();
			}
			
			array_push($Bouts, $BoutArray);
		}
		
		if(empty($Bouts))
		{
			$weightClasses = [];
			if(strpos($competition["ligaId"], '(S)') === 0)
			{
				$weightClasses = [
					["29", "LL"],
					["33", "GR"],
					["36", "LL"],
					["41", "GR"],
					["46", "LL"],
					["50", "GR"],
					["60", "LL"],
					["76", "GR"]
				];
			}
			elseif($competition["ligaId"] == "Oberliga" || $competition["ligaId"] == "Bayernliga")
			{
				$weightClasses = [
					["57", "LL"],
					["61", "GR"],
					["66", "LL"],
					["71", "GR"],
					["75 A", "LL"],
					["75 B", "GR"],
					["80", "LL"],
					["86", "GR"],
					["98", "LL"],
					["130", "GR"]
				];
			}
			else
			{
				$weightClasses = [
					["57", "LL"],
					["61", "GR"],
					["66", "LL"],
					["75", "GR"],
					["86", "LL"],
					["98", "GR"],
					["130", "LL"],
					["57", "GR"],
					["61", "LL"],
					["66", "GR"],
					["75", "LL"],
					["86", "GR"],
					["98", "LL"],
					["130", "GR"]
				];
			}
			
			$i = 1;
			foreach ($weightClasses as $weightClass) {
				$Bout = new Bout();
				$Bout->saisonId = $sid;
				$Bout->competitionId = $cid;
				$Bout->order = $i++;
				$Bout->weightClass = $weightClass[0];
				$Bout->style = $weightClass[1];
				$Bout->homeWrestlerPoints = "0";
				$Bout->opponentWrestlerPoints = "0";
				$Bout->round1 = "";
				$Bout->round2 = "";
				$Bout->round3 = "";
				$Bout->round4 = "";
				$Bout->round5 = "";
				
				array_push($Bouts, ($Bout)->toArray());	
			}
			
			
	

	
		}

		

		
		
		$competition["_boutList"] = $Bouts;
	
		$databaseConnection->Close($conn);

		return array('rpcid' => null, 'rc' => 'ok', 'api' => array('rdb' => '3.0.8 \/ 3.0.9','jrcs' => '1.0.3'), 'year' => '2021','sid' => $sid,'cid' => $cid,'flags' => 0, 'competition' => $competition);
	}



	private function GetSaisonWrestler($passcode)
	{
		if (!BasicAuthUsers::CheckLogonUser($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']))
		{
			throw new ApiException('User '.$_SERVER['PHP_AUTH_USER'].' is not allowed or has wrong password.', ApiException::AUTHENTICATION_FAILED);
		}

		$databaseConnection = new DatabaseConnection();
		$conn = $databaseConnection->Connect();		

		$dbResults = $conn->query("SELECT * FROM jos_rdb_wrestler WHERE Id='rdb.".$passcode."'");

		$wrestler = $dbResults->fetch_object('Api\Entity\wrestler');
		
		$databaseConnection->Close($conn);

		if($wrestler == null)
			throw new ApiException('Passcode '.$passcode.' not valid.', ApiException::MALFORMED_INPUT);
		else
			return array('rpcid' => null, 'rc' => 'ok', 'api' => array('rdb' => '3.0.8 \/ 3.0.9','jrcs' => '1.0.3'), 'year' => '2021','sid' => '2021','passcode' => $passcode,'nationCode' => "GER",'authCode' => "BRV", 'wrestler' => ($wrestler)->toArray());

	}
	

	/**
	 * @param array $data
	 *
	 * @return array
	 * @throws ApiException
	 */
	private function UpdateCompetition(array $data, $saisonId, $competitionId)
	{
			throw new ApiException('Function not implemented.', ApiException::NOT_IMPLEMENTED);
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	private function delete($id)
	{
		// Benutzer in der Datenbank löschen
		// ...

		return [];
	}
}