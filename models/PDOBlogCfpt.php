<?php

class PDOBlogCfpt
{
    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=db_BlogCfpt';
    private static $user = 'root';
    private static $mdp = '';
    private static $PDOBlogCfpt;
    private static $conn = null;

    // Constructeur privé, crée l'instance de PDO qui sera solicité
    // pour toutes les méthodes de la classe

    private function __construct()
    {
        PDOBlogCfpt::$conn = new PDO(PDOBlogCfpt::$serveur . ";" . PDOBlogCfpt::$bdd, PDOBlogCfpt::$user, PDOBlogCfpt::$mdp);
        PDOBlogCfpt::$conn->query('SET names utf8');
        PDOBlogCfpt::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function __destruct()
    {
        PDOBlogCfpt::$conn = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe 
     * Appel : $instancePDO = PDO::getPDO();
     *
     * @return l'unique objet de la classe PDO
     */
    public static function getInstance()
    {
        if (self::$conn == null) {
            self::$PDOBlogCfpt = new PDOBlogCfpt();
        }
        return self::$conn;
    }
}
?>