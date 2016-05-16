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

###Permalänkar nätverkslösning
Kontrollera att permalänkar är rätt inställda för huvudwebbplatsen, för korrekt permalänkstruktur i denna nätversklösning.
- Klicka på "Inställningar > Permalänkar" och välj "Förvald".
- Klicka på "Gå till nätverksadmin" och klicka på "Mina webbplatser".
- För musen över huvudsajtens namn och välj "Redigera" och sedan tabben "Inställningar".
- Leta reda på fältet "Permalink Structure" och lägg till /%postname%/.
- Spara ändringar.


###Versionsnoteringar
Utveckla i dev gren. Sammanfoga till master vid ny release och uppdatera versionsnummer enligt nedan struktur.

*Större ändringar . Antal ändringar och nya funktioner . Antal åtgärdade buggar*

####1.2.1
**Ändringar**

- Utökade behörigheter för att tillgängliggöra en administratör att skapa och hantera bloggar för nätverket.

**Buggfix**

- Korrigeringen av föråldrad funktion (depricated function notice).

####1.1.0
Utskriftsvänliga sidor

####1.0.0
Första relasen