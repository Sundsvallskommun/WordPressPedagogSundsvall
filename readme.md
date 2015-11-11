#Child Theme - Pedagog Sundsvall
Temat använder ACF fält utöver standard vilket innebär att det behöver importeras följande fält förutom de som ligger i huvudtemat för Pedagog Sundsvall. Finns placerade i foldern acf-fields.

**Steg 1**

1. Importera ACF fält från huvudtema, [https://github.com/Sundsvallskommun/WordPressBaseTheme](http://)
2. Radera "Innehållsblock"

**Steg 2**

Importera följande ACF fält från foldern acf-fields i detta tema.


- acf-innehallsblock.json (ska ersätta innehållsblock från WordPressBaseTheme)
- acf-pedagog-backup.json
- acf-vansterspalt.json
- acf-senaste-poster-natverk.json
- acf-kalender.json
- acf-paneler.json
- acf-blogginstallningar.json

###Versionsnoteringar
Utveckla i dev gren. Sammanfoga till master vid ny release och uppdatera versionsnummer enligt nedan struktur.

*Större ändringar . Antal ändringar och nya funktioner . Antal åtgärdade buggar*

####1.0.0
Första relasen