<?php
namespace App\Model;
use Nette;

/*!
 * \class Base
 * \brief Hlavní model volající databázi
 */
abstract class Base extends Nette\Object
{
        protected $database;

        public function __construct(Nette\Database\Context $database)
        {
                $this->database = $database;
        }
}

