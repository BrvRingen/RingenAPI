<?php
namespace Api\Controller;

use Api\Library\ApiException;
use Api\Entity\Competition;
use Api\Entity\Bout;
use Api\Entity\Cs;
use Api\Entity\Table;
use Api\Entity\Startausweis;
use Api\Database\DatabaseConnection;

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
				//curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?saisonId=2019&ligaId=Oberliga&tableId=Nord"
				//curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?saisonId=2019&ligaId=(S)+Bezirksliga&tableId=Oberbayern
				//curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?saisonId=2019&ligaId=Aufstiegsk%c3%a4mpfe&tableId=Landesliga+S%c3%bcd"
				
				//curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?saisonId=2019"

				//curl -X GET "https://www.brv-ringen.de/Api/v1/cs/"

				//curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?competitionId=018001r"
				
				//curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?startausweisNr=4440&saisonId=2019&competitionId=018001r"

				if(isset($_GET['startausweisNr']) && isset($_GET['saisonId']) && isset($_GET['competitionId']))
					$result = $this->GetStartausweis($_GET['startausweisNr'],$_GET['saisonId'], $_GET['competitionId']);
				elseif(isset($_GET['saisonId']) && isset($_GET['ligaId']) && isset($_GET['tableId']))
					$result = $this->GetCompetition($_GET['saisonId'], $_GET['ligaId'], $_GET['tableId']);
				elseif(isset($_GET['saisonId']) && isset($_GET['competitionId']))
					$result = $this->GetBout($_GET['saisonId'], $_GET['competitionId']);
				elseif(isset($_GET['saisonId']))
					$result = $this->GetTable($_GET['saisonId']);
				else
					$result = $this->GetCs();
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
			elseif ($e->getCode() == ApiException::UNKNOWN_ID)
				header('HTTP/1.0 400 Bad Request');
				
			$result = ['message' => $e->getMessage()];
		}
		
		header('Content-Type: application/json');
		echo json_encode($result); //JSON_PARTIAL_OUTPUT_ON_ERROR
	}

	/**
	 * @return array
	 */
	private function GetCs()
	{
		$databaseConnection = new DatabaseConnection();
		$conn = $databaseConnection->Connect();		
		$dbResults = $conn->query("SELECT saisonId FROM jos_rdb_cs");
		
		$Css = [];
		$i = 0;
		while ($Cs = $dbResults->fetch_object('Api\Entity\Cs')) {
			$Css[$i++] = ($Cs)->toArray();
		}
		
		$databaseConnection->Close($conn);

		return $Css;
	}
	
	/**
	 * @param int $CompetitionId
	 *
	 * @return array
	 */
	private function GetTable($saisonId)
	{
		$databaseConnection = new DatabaseConnection();
		$conn = $databaseConnection->Connect();		
		$dbResults = $conn->query("SELECT * FROM jos_rdb_cs__table WHERE saisonId='".$saisonId."'");
		
		$Tables = [];
		$i = 0;
		while ($Table = $dbResults->fetch_object('Api\Entity\Table')) {
			$Tables[$i++] = ($Table)->toArray();
		}
		
		$databaseConnection->Close($conn);

		return $Tables;
	}
	

	/**
	 * @param int $CompetitionId
	 *
	 * @return array
	 */
	private function GetCompetition($saisonId, $ligaId, $tableId)
	{
		$databaseConnection = new DatabaseConnection();
		$conn = $databaseConnection->Connect();		

		$dbResults = $conn->query("SELECT * FROM jos_rdb_cs__competition WHERE saisonId='".$saisonId."' AND ligaId='".$ligaId."' AND tableId='".$tableId."'");

		$Competitions = [];
		$i = 0;
		while ($Competition = $dbResults->fetch_object('Api\Entity\Competition')) {
			$Competitions[$i++] = ($Competition)->toArray();
		}
		
		$databaseConnection->Close($conn);

		return $Competitions;
	}
	
	/**
	 * @param int $CompetitionId
	 *
	 * @return array
	 */
	private function GetBout($saisonId, $competitionId)
	{
		$databaseConnection = new DatabaseConnection();
		$conn = $databaseConnection->Connect();		

		$dbResults = $conn->query("SELECT * FROM jos_rdb_cs__bout WHERE saisonId='".$saisonId."' AND competitionId='".$competitionId."'");

		$Bouts = [];
		$i = 0;
		while ($Bout = $dbResults->fetch_object('Api\Entity\Bout')) {
			$Bouts[$i++] = ($Bout)->toArray();
		}
		
		$databaseConnection->Close($conn);

		return $Bouts;
	}

	private function GetStartausweis($startausweisNr , $saisonId, $competitionId)
	{
		
		if (!($_SERVER['PHP_AUTH_USER'] == 'xxx' and $_SERVER['PHP_AUTH_PW'] == 'xxx'))
		{
			throw new ApiException('User'.$_SERVER['PHP_AUTH_USER'].' is not allowed or has wrong password.', ApiException::AUTHENTICATION_FAILED);
		}

		$databaseConnection = new DatabaseConnection();
		$conn = $databaseConnection->Connect();		

		$dbResults = $conn->query("SELECT name, givenname, status, birthday FROM jos_rdb_wrestler WHERE Id='rdb.".$startausweisNr."'");

		$Startausweis = $dbResults->fetch_object('Api\Entity\Startausweis');
		
		$databaseConnection->Close($conn);

		return ($Startausweis)->toArray();
	}
	
	
	/**
	 * @param array $data
	 *
	 * @return array
	 */
	private function create(array $data)
	{
		// Benutzer anlegen ...
		$user           = new User();
		$user->username = $data['username'];

		// ... und in der Datenbank speichern

		// Für das Beispiel:
		$user->id = rand(100, 999);

		return ['id' => $user->id];
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 * @throws ApiException
	 */
	private function UpdateCompetition(array $data, $saisonId, $competitionId)
	{
		if ($_SERVER['PHP_AUTH_USER'] != 'test' or $_SERVER['PHP_AUTH_PW'] != 'test')
		{
			throw new ApiException('User'.$_SERVER['PHP_AUTH_USER'].' is not allowed or has wrong password.', ApiException::AUTHENTICATION_FAILED);
		}

		$databaseConnection = new DatabaseConnection();
		$conn = $databaseConnection->Connect();		

		$conn->query("UPDATE jos_rdb_cs__competition SET editorComment = '".$data['editorComment']."', audience = '".$data['audience']."' WHERE saisonId='".$saisonId."' AND competitionId='".$competitionId."'");
				
		$databaseConnection->Close($conn);
		$return['status'] = 'updated';
		$return['updatedProperties'] = ['editorComment', 'audience'];
		return $return;
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