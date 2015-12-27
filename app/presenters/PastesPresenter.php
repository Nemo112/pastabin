<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI;

/*!
 * \class PastesPresenter
 * \brief Prezentér pro pasty
 */
class PastesPresenter extends BasePresenter
{
    /*! \var $pasters
        \brief Reprezentace modelu Pasters
    */
	private $pasters;
    /*! \fn injectModels($Pasters)
        \param[in] $Pasters injekce modelu
        \brief Metoda vytvoří pomocí injekce model mapující databázi
    */
   	public function injectModels(Model\Pasters $Pasters){
		$this->pasters = $Pasters;
	}
    /*! \fn renderDefault()
        \brief Metoda pro konstrukci proměnných určených pro pohledy
    */
	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}
    /*! \fn actionNothing()
        \brief Metoda pro vrácení chyby uživateli přes kontroler flashMessage
    */
	public function actionNothing(){
		$this->flashMessage('Paste nenalezen');
	}
    /*! \fn actionShow($id)
        \param[in] $id id Pastu
        \brief Metoda z databáze vytáhne všechny pasty podle ID
    */
	public function actionShow($id){
	        $line = $this->pasters->findBy(array(
        	        "id" => $id,
        	))->fetch();
		if (empty($line)){
			$this->redirect('nothing');
		}else{
			$this->template->content = $line->value;
			$this->template->datu = date('d.m.Y H:i:s',strtotime($line->time));
		}
	}
}
