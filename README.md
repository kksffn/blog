#Czenglish blog#

Trochu větší projekt v rámci PHP kurzu. Cílem bylo vytvořit stránku - "blog".
Šlo o to naučit se víc věcí, proto některé věci dělám různě a nedržím se jedné šablony. Není to OOP...

1. Aby vše fungovalo, je potřeba nastavit blog/_inc/config.php:
	- řádek 15: adresa domovské stránky BASE_URL
	- řádky 28-35: údaje o DB
	- řádek 26 - používám aktivaci přes email - SMTP je možné nastavit v DB (phpauth_config, řádky "smtp",....) nebo dát aktivaci na false, každopádně se lze přihlásit jako admin:
	nickname: ivo, heslo ivo123; přihlašovací údaje pro Edu (editor) a Fida (user) jsou v postech.

2. Přes kompostér jsem natáhl a v app využívám
	- tamtamchik (flash hlášky - doplněné v js, aby mizely a daly se odkřížkovat)
	- phpauth (POZOR: trochu jsem upravil některé metody, které se týkaly přihlašování - mám v DB přidané sloupce)
	- phpmailer
	- (bjeavons a google recaptcha nevyužívám)

3. Využívám MySQL DB: blog/blog_220_08_24.sql
---
4. index.php funguje jako jednoduchý router
5. K připojení k DB používám PDO (nastavení v config.php)
6. Stránka funguje jako "blog" - na homepage jsou vylistované všechny příspěvky s autory, daty, tagy, počtem zhlédnutí a komentářů. 
Dají se zobrazit příspěvky podle autora, tagu. Dá se napsat nový post, editovat existující, vymazat existující. To umí admin, editor nebo autor.
K postu se dá připojit do header background image.
7. PHPAuth. Údaje o přihlášeném drží cookies. Různí uživatelé mají různá práva (admin, editor, user - ENUM v DB). Aktivace přes email.
Uživatel může měnit svoje údaje, admin může dávat ban a měnit údaje všem, nemůže smazat posledního admina,...
TODO: POKUD BUDE ADMIN MĚNIT PRÁVA DVĚMA ADMINŮM NAJEDNOU A ŽÁDNÝ NEZBYDE... 
---
8. Úkol 13:
	a) tagy:admin může tagy editovat a přidávat přes formulář (_admin/tags-control.php - pro každý tag dva formuláře - edit, delete), post request vede na stránky 
		_admin/add-tag.php, _admin/delete-tag.php nebo _admin/edit-tag.php. 
		Zároveň přidaná obsluha pře js/ajax (po kliknutí na tag se na stránku přidá input s id a script(za to může metoda v assets/app.js), který obslouží editaci (assets/js/edit_tag.js)).. 
		Nepřišel jsem na lepší řešení, než načíst další soubor se scriptem, který pozná, co bylo na stránce editováno...
		Při vkládání nebo editaci postu může autor naklikat existující tagy a/nebo napsat nové (input - oddělovač je čárka).
		_inc/functions-tag.php
	
	b) komentáře: nová tabulka v DB: comments navázaná na posts přes post_id; u článku je počet komentářů - vypsáni autoři a datum, komentář lze odeslat pomocí ctrl+enter, obsluha přes 
		asssets/app.js. Komentáře jsem nedovolil mazat ani editovat, to mi nepřijde jako dobrý nápad v diskuzi... Komentáře jsou samozřejmě validované a sanitizované
		_inc/functions-comments.php
	
	c) přidávání obrázků do post-header: nová tabulka v DB: images navázan na posts. Původně jsem chtěl obsluhu obrázků udělat přes Intervention Image, nakonec jsem ale metody napsal sám. 
		Při přidávání nebo editaci článku má user možnost uploadovat obrázek - max. 2MB, nahraje se do assets/img/post/{post_id} a udělá se záznam v DB, při změně obrázku se záznam updatuje
		obrázků přidávám čísla přes pomlčku, aby nebyly dva soubory se stejným jménem.
        V DB jsou přidané TRIGGERs - při smazání příspěvku se smažou záznamy v posts_tags, comments, images a složka s obrázky se vymaže z disku
        _inc/functions-image.php