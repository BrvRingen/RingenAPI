# RingenAPI
API für das Programm Ringen

- - -

## Beispiele

### Abfragen aller Jahre

`curl -X GET "https://www.brv-ringen.de/Api/v1/cs/"`

### Abfrage aller Tabellen eines Jahres

`curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?saisonId=2019"`


### Abfrage einer Tabelle

`curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?saisonId=2019&ligaId=Oberliga&tableId=Nord"`

`curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?saisonId=2019&ligaId=(S)+Bezirksliga&tableId=Oberbayern`

`curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?saisonId=2019&ligaId=Aufstiegsk%c3%a4mpfe&tableId=Landesliga+S%c3%bcd"`


### Abfragen eines Kampfes

`curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?saisonId=2019&competitionId=018001r"`


### Abfrage von Kampf-Details

`curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?saisonId=2019&competitionId=018001r&order=1"`


### Abfrage eines Ringers (Nur mit Anmeldung!!!)

`curl -X GET "https://www.brv-ringen.de/Api/v1/cs/?startausweisNr=4440&saisonId=2019&competitionId=018001r"`
