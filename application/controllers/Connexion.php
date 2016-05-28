<?php 
	class Connexion extends Controleur {
		
		function Login() {
			
			if(!empty($_POST['mail']) && !empty($_POST['pass']) && !empty($_POST['passphrase'])) {
				$sql = "SELECT * FROM utilisateur WHERE pseudo = :pseudo AND password = :password AND passPhrase = :passPhrase";
				$newUser = new mUtilisateur();
                $newUser->setPseudo($_POST['mail']);
                $newUser->setPassword($_POST['pass']);
                $newUser->setPassPhrase($_POST['passphrase']);
                $newUser->setRequete($sql);

                $user = $newUser->Connection();
              	if(!empty($user)) {
              		
              		foreach($user as $key => $s)
              		{
              			$_SESSION['Utilisateur']['idUtilisateur'] = $s->getidUtilisateur();
              			$_SESSION['Utilisateur']['passPhrase'] = $s->getPassPhrase();
              			$_SESSION['Utilisateur']['Pseudo'] = $s->getPseudo();
              		}
              		
              		echo "ok";
              	}else {
					
				}
			} 
		}
		
		function getPublicKey() {
			$filePublic = "../proton/".$_SESSION['Utilisateur']['idUtilisateur']."/publicKey.gpg";
				
			$contenuPublic  = file_get_contents($filePublic,FILE_USE_INCLUDE_PATH);
			echo $contenuPublic;
			
		}
		
		function getPrivateKey() {
			
			$filePrivate = "../proton/".$_SESSION['Utilisateur']['idUtilisateur']."/privateKey.gpg";
			$contenuPrivate = file_get_contents($filePrivate,FILE_USE_INCLUDE_PATH);
			echo $contenuPrivate;
		}
		
		function getToken() {
			echo $_SESSION['Utilisateur']['passPhrase'];
		}
		
		function DestroySession() {
			unset($_SESSION['Utilisateur']['idUtilisateur']);
			unset($_SESSION['Utilisateur']['passPhrase']);
			unset($_SESSION['Utilisateur']);
		}
	}
?>