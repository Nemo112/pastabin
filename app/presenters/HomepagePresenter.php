<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI;
use Nette\Security\User;

/*!
 * \class HomepagePresenter
 * \brief Prezentér pracující s hlavní stránkou
 */
class HomepagePresenter extends BasePresenter
{
    /*! \var $userManager
        \brief Reprezentace modelu pracujícího s uživateli
    */
	private $userManager;
    /*! \var $pasters
        \brief Reprezentace modelu Pasters
    */
	private $pasters;
    /*! \fn injectModels($Pasters,$userManager)
        \param[in] $Pasters injekce modelu
        \param[in] $userManager injekce modelu
        \brief Metoda vytvoří pomocí injekce model mapující databázi
    */
    public function injectModels(Model\Pasters $Pasters,Model\UserManager $userManager){
		$this->pasters = $Pasters;
		$this->userManager = $userManager;
	}
    /*! \fn renderDefault()
        \brief Metoda pro konstrukci proměnných určených pro pohledy
    */
	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
		$user = $this->getUser();
		if($user->isLoggedIn()){
        	$this->template->pasty = $this->pasters->getByUser($user->id);
		}
	}
    /*! \fn createComponentPasteForm()
        \brief Metoda pro konstrukci komponenty formuláře. Nastaví relaci na JavaScript funkci adjust
    */
	protected function createComponentPasteForm()
    {
        $form = new UI\Form;
		$form->addTextArea('note', 'Paste:')->setAttribute('onkeyup','adjust(this)');
		$form['note']->getControlPrototype()->addClass('add-form');
        $form->addSubmit('login', 'Odeslat');
        $form->onSuccess[] = array($this, 'pasteFormSucceeded');
        return $form;
    }
    /*! \fn createComponentLoginForm()
        \brief Metoda volaná po odeslání formuláře
    */
	protected function createComponentLoginForm()
    {
        $form = new UI\Form;
		$form->addText('username', 'Jméno:');
		$form->addPassword('password', 'Heslo:');
		$form->addCheckbox('regist','Registrovat');
		$form->addSubmit('login', 'Přihlásit');
        $form->onSuccess[] = array($this, 'loginFormSucceeded');
        return $form;
    }
   /*! \fn loginFormSucceeded($form, $values)
        \param[in] $form objekt formuláře
        \param[in] $values data z formuláře
        \brief Metoda vyhodnotí výsledky odeslaného formuláře
    */
    public function loginFormSucceeded(UI\Form $form, $values)
    {
		$user = $this->getUser();
		try {
			$user->login($values->username, $values->password);
		} catch (Nette\Security\AuthenticationException $e){
			if ($e->GetMessage() == 'Not in database' || $e->GetMessage() == 'The username is incorrect.'){
				if($values->regist){
					$this->userManager->add($values->username, $values->password);
					$this->flashMessage("První přihlášení");
					return $this->loginFormSucceeded($form, $values);
				}else{
					$this->flashMessage("Bohužel, nejste registrovaný! Povolte registraci.");
				}				
			}else{
				$this->flashMessage("Bohužel, nejste registrovaný!");
			}			
		}
		if($user->isLoggedIn()){
        	$this->template->pasty = $this->pasters->getByUser($user->id);
			$this->flashMessage("Jste přihlášen jako ".$user->getIdentity()->data["username"]);
		}
	}
    /*! \fn actionLogout()
        \brief Metoda odhlásí uživatele
    */
    public function ActionLogout(){
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení bylo úspěšné.');
		$this->redirect('Homepage:');
	}
    /*! \fn actionRemoveme()
        \brief Metoda odhlásí a smaže uživatele i s pasty
    */
    public function ActionRemoveme(){
		$user = $this->getUser();
		$this->userManager->rem($user->getIdentity()->data["username"]);
		$this->pasters->delByUser($user->id);
		$this->getUser()->logout();
		$this->flashMessage('Smazání bylo úspěšné.');
		$this->redirect('Homepage:');
	}
   /*! \fn loginFormSucceeded($form, $values)
        \param[in] $form objekt formuláře
        \param[in] $values data z formuláře
        \brief Metoda vloží paste do databáze a odešle uživatele na další prezentér
    */
    public function pasteFormSucceeded(UI\Form $form, $values){
		$user = $this->getUser();
		if($user->isLoggedIn())
			$this->pasters->add($values->note,$user->id);
		else	
			$this->pasters->add($values->note,0);
		$line = $this->pasters->findBy(array(
			"value" => $values->note,
		))->fetch();
		    $this->flashMessage('Paste vložen');
		$this->redirect('Pastes:show',$line->id);
    }
}
