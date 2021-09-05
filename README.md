# RingenAPI
API für das Programm Ringen

- - -

## Beispiele

curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?op=listSaison"
curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?op=listLiga&sid=2019"
curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?op=listCompetition&sid=2021&ligaId=Gruppenliga&rid=Süd"
curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?op=getCompetition&sid=2019&cid=001006a"
curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?op=getCompetition&sid=2021&cid=109110j"

Nur mit Anmeldung
curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?op=getRinger&sid=2021&cid=109110j&startausweisnummer=4440"
