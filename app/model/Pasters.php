<?php

namespace App\Model;

use Nette;
use Nette\Utils\Strings;

/*!
 * \class Pasters
 * \brief Model reprezentující entitu pastes z databáze
 */
class Pasters extends Base
{
    /*! \var TABLE_NAME
        \brief Jméno tabulky
    */
	const TABLE_NAME = 'pastes';
    /*! \fn add($value,$id_user)
        \param[in] $value data pastu
        \param[in] $id_user id uživatele
        \brief Metoda vytvoří paste v databázi a vyhodí excepšnu při chybě
    */
	public function add($value, $id_user)
	{
		try {
			$this->database->table(self::TABLE_NAME)->insert(array(
				"value" => $value,
				"id_user" => $id_user,
			));
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}
    /*! \fn findBy($by)
        \param[in] $by data pro hledání
        \brief Metoda prohledá tabulku podle kritérií v $by
		\return Řádek tabulky podle vyhledávání
    */
	public function findBy(array $by) {
		    return $this->getTable()->where($by);
	}
    /*! \fn update($data)
        \param[in] $data pro změnu
        \brief Metoda změní data podle vstupního id
    */
	public function update($data){
		$this->findBy(array('id'=>$data['id']))->update($data);
	}
    /*! \fn del($id)
        \param[in] $id záznamu ke smazání
        \brief Metoda smaže záznam podle $id
    */
	public function del($id)
	{
		$this->database->table(self::TABLE_NAME)->where('id',$id)->delete();
	}
    /*! \fn delByUser($userid)
        \param[in] $userid id uživatele
        \brief Metoda smaže vše, co patřilo uživateli podle id
    */
	public function delByUser($userid)
	{
		$this->database->table(self::TABLE_NAME)->where('id_user',$userid)->delete();
	}
    /*! \fn getTable()
        \brief Metoda vrátí celou tabulku
		\return tabulka pastes
    */
	public function getTable() {
		    return $this->database->table(self::TABLE_NAME);
	}
    /*! \fn getLine($id)
        \param[in] $id id řádku
        \brief Metoda vrátí řádek podle id
		\return řádek podle id
    */
	public function getLine($id) {
		    return $this->database->table(self::TABLE_NAME)->where('id',$id)->fetch();
	}
    /*! \fn getByUser($id)
        \param[in] $id id uživatele
        \brief Metoda vrátí vše podle id uživatele
		\return řádek podle id uživatele
    */
	public function getByUser($id) {
		    $fth = $this->database->table(self::TABLE_NAME)->where('id_user',$id)->fetchAll();
		    return $fth;
	}
    /*! \fn getLineByUserId($id)
        \param[in] $id id uživatele
        \brief Metoda vrátí vše podle id uživatele
		\return řádek podle id uživatele
    */
	public function getLineByUserId($id) {
		    return $this->database->query("
				SELECT * FROM
				pastes
				WHERE pastes.id_user = ".$id."
			;");
	}
}
