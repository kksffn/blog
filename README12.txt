Domáca úloha #12
Rozšírenie PHPAuth
Máme registráciu, máme prihlásenie cez email, máme odhlásenie a máme kontrolu, kto je prihlásený. To je fajn, ale to je asi tak celé.

Na náš projekt to stačí, ale v rámci zbierania skúseností by som chcel, aby ste:

1. pridali možnosť evidovať a zobrazovať username (teda meno, nielen email)
2. pridali role (admin, editor, user)
3. rozchodili aktiváciu cez email, rozchodili password reset a teda rozchodili rozosielanie emailov

Kde user je to, čo máme teraz. Editor môže editovať a mazať všetky príspevky každého používateľa. Admin môže všetko, plus môže mazať/zabanovať userov.

Na role si naštudujte a použite MySQL ENUM typ poľa.

Bude to chvíľu trvať, bude to väčší projekt, ale naučíte sa veľa:) Choďte do toho.
-------------------------------------------------------------------------------------------------------------------------------------------------------

ad 1. 	V DB v tabulce phpauth_users přidány sloupce: nickname, about_me, rights (EBUM 'admin', 'editor', 'user'), ban. 

	Metoda format_text(...) nickname ošetřuje. 
	Metody validateNickname($nickname) a isNicknameTaken($nickname) v Auth.php.
	Změněny metody register(), addUser().

	Na stránkách home, post, tag, user zobrazován nickname i email.

	V DB i languages přidány pro GB a CZ hlášky pro nickname.

	Do formuláře pro login může uživatel zadat email NEBO nickname (pro rozlišení jsem přidal podmínku, že nickname neobsahuje zavináč). Podle zavináče pozná 
	metoda login(), zda byl zadán nickname, nebo email. 
	Metoda login() navíc ověřuje, zda uživatel nemá ban.

	Nová metoda getEmailForNickname($nickname)

ad 2. 	Posty může mazat a editovat i admin a editor.
	Na stránkách v header.php přidáno dropdown menu: edit users (pouze admin), edit all user's posts (pouze editor), your profile, your settings, logout.
	edit users vede na stránku admins-page: admin zde může editovat údaje o uživatelích (nickname, email, rights, ban; edit user, edit user's posts).
	
	Formulář se odesílá najednou, ne po jednotlivých uživatelích (to mi přijde user friendly) - využitý javascript (onchange="add_input(this)"..., 
	do DB se ale queries posílají zvlášť, tedy se může stát, že některé údaje se změní, některé při chybě ne - o všem je potom uživatel informován přes 
	flash()->... Admin nemůže změnit práva sám sobě a nemůže se smazat, pokud by nezbýval žádný admin.

	Analogicky editor má stránku editors-page, kde může smazat posty uživatele všechny najednou. Na obě stránky nemá přístup user.
	Při smazání uživatele přejdou všechny jeho posty na uživatele Anonymous, který se nevypisuje (nejde tedy vymazat).
	Delete linky vedou na stránky deleteuser a post na user_delete. Zabráněno zobrazení formuláře pro neexistujícího uživatele. Zabráněno mazání posledního admina.
	Přidány stránky edit-users-post-form a edit-users-posts
	Nové metody:
		get_users()
		get_admins_link() a
		get_editors_link() vytvářejí linky na stránky pro admina a editora
		isEmailTakenBySomeoneElse($email, $id)							
		changeEmailWithoutPassword($user_id, $email) a
		update_user($user_id, $data) v Auth.php - ošetření vstupů z formuláře od admina a zavolání updateUser(...), která už existovala.							
		
	Uživatelům je k dispozici stránka profile ke změně jména, emailu, about_me; nové metody validate_user_info() a create_session_user_data() - pro znovuvyplnění
	formuláře.
	Settings dovolí pouze nastavení nového hesla - využita metoda z Auth.php.

ad 3. 	Aktivaci účtu přes mail a reset hesla jsem rozchodil přes phpmailer a gmail, to jsem ale zrušil, je potřeba uchovávat přihlašovací údaje; 
	následně změnil a rozchodil přesmailtrap.io - stránka vygeneruje email, ten ale uživateli nedorazí, vše ale funguje a mnohokrát jsem si to ověřil.
	
	Úspěšná registrace přesměruje uživatele na activate, kde se vloží aktivační klíč, až potom redirect na /login; přidána stránka reactivate (využije se 
	při vypršení klíče).
	Reset hesla - stránka reset-request je formulář , kde se zadá email, na který se pošle reset-key pro změnu hesla; reset.php je stránka s formulářem, kam se 
	vloží klíč a nové heslo, pak redirect na login a poslání nových přihlašovacích údajů mailem (nové metody getUserFromToken($reset_key) pro získání $user z vygenerovaného 
	klíče a sendNewLoginData($user, $new_pass) pro poslání informačního emailu o změně hesla s přístupovými údaji).
