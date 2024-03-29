<?php
class Media
{
    // *********** variables ************
    private $idMedia;
    private $typeMedia;
    private $nomFichierMedia;
    private $creationDate;
    private $modificationDate;
    private $idPost;
    private $compteurMedia;

    // *********** Properties ************
    /**
     * Get the value of idMedia
     */
    public function getIdMedia()
    {
        return $this->idMedia;
    }

    /**
     * Set the value of idMedia
     *
     * @return  self
     */
    public function setIdMedia($idMedia)
    {
        $this->idMedia = $idMedia;

        return $this;
    }

    /**
     * Get the value of typeMedia
     */
    public function getTypeMedia()
    {
        return $this->typeMedia;
    }

    /**
     * Set the value of typeMedia
     *
     * @return  self
     */
    public function setTypeMedia($typeMedia)
    {
        $this->typeMedia = $typeMedia;

        return $this;
    }

    /**
     * Get the value of nomFichierMedia
     */
    public function getNomFichierMedia()
    {
        return $this->nomFichierMedia;
    }

    /**
     * Set the value of nomFichierMedia
     *
     * @return  self
     */
    public function setNomFichierMedia($nomFichierMedia)
    {
        $this->nomFichierMedia = $nomFichierMedia;

        return $this;
    }

    /**
     * Get the value of creationDate
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set the value of creationDate
     *
     * @return  self
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get the value of idPost
     */
    public function getIdPost()
    {
        return $this->idPost;
    }

    /**
     * Set the value of idPost
     *
     * @return  self
     */
    public function setIdPost($idPost)
    {
        $this->idPost = $idPost;

        return $this;
    }

    /**
     * Get the value of modificationDate
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * Set the value of modificationDate
     *
     * @return  self
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }

    /**
     * Get the value of compteurMedia
     */
    public function getCompteurMedia()
    {
        return $this->compteurMedia;
    }

    /**
     * Set the value of compteurMedia
     *
     * @return  self
     */
    public function setCompteurMedia($compteurMedia)
    {
        $this->compteurMedia = $compteurMedia;

        return $this;
    }

    // *********** Functions ************

    // Récupère tous les médias en fonction de l'id du post
    public static function getAllMediasByPostId($idPost)
    {
        $req = PDOBlogCfpt::getInstance()->prepare("SELECT * FROM media WHERE idPost = :idPost;");
        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Media'); // methode de fetch
        $req->bindParam(":idPost", $idPost);
        $req->execute(); // executer la requette

        $lesResultats = $req->fetchAll();
        return $lesResultats;
    }

    // Ajoute une image dans la base de données
    public static function AddMedia(Media $media)
    {
        $typeMedia = $media->getTypeMedia();
        $nomFichierMedia = $media->getNomFichierMedia();
        $creationDate = $media->getCreationDate();
        $modificationDate = $media->getModificationDate();
        $idPost = $media->getIdPost();
        $req = PDOBlogCfpt::getInstance()->prepare("INSERT INTO media(typeMedia, nomFichierMedia, creationDate, modificationDate, idPost) VALUES(:typeMedia, :nomFichierMedia, :creationDate, :modificationDate, :idPost);");
        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Media'); // methode de fetch
        $req->bindParam(":typeMedia", $typeMedia);
        $req->bindParam(":nomFichierMedia", $nomFichierMedia);
        $req->bindParam(":creationDate", $creationDate);
        $req->bindParam(":modificationDate", $modificationDate);
        $req->bindParam(":idPost", $idPost);
        $req->execute(); // executer la requette

    }

    public static function DeleteMedia($idMedia)
    {
        $req = PDOBlogCfpt::getInstance()->prepare("DELETE FROM media WHERE idMedia = :idMedia");
        $req->bindParam(":idMedia", $idMedia);
        $req->execute();
    }

    // récupère le nom du fichier en fonction de son id
    public static function GetMediaNameById($idMedia)
    {
        $req = PDOBlogCfpt::getInstance()->prepare("SELECT nomFichierMedia FROM media WHERE idMedia = :idMedia");
        $req->bindParam(":idMedia", $idMedia);
        $req->execute();
        $result = $req->fetch();
        return $result['nomFichierMedia'];
    }


    public static function ConvertOctetsToMO($octets)
    {
        // 1mo = 1 048 576 octets
        return $octets / 1000000;
    }

    public static function GenerateRandomImageName()
    {

        $alphabet = range('a', 'z');
        $newImageName = "";
        for ($i = 0; $i < 26; $i++) {
            $newImageName .= $alphabet[rand(0, 25)];
        }

        return $newImageName;
    }

    public static function CountAllMediasPerPost($iPost)
    {
        $req = PDOBlogCfpt::getInstance()->prepare("SELECT COUNT(idMedia) as 'compteurMedia' FROM media WHERE idPost = :idPost;");
        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Media');
        $req->bindParam(':idPost', $iPost);
        $req->execute(); // executer la requette
        $result = $req->fetch();
        return $result->getCompteurMedia();
    }

    public static function deleteAllFilesThatAreNotInBase(){
        $req = PDOBlogCfpt::getInstance()->prepare("SELECT nomFichierMedia FROM media");
        $req->execute(); // executer la requette
        $result = $req->fetchALL(PDO::FETCH_ASSOC);
        $dir = scandir("C:\\xampp\\htdocs\\M152\\assets\\medias");

        foreach($dir as $file){
            if($file != "." && $file != ".."){
                $isInBase = false;
                foreach($result as $fileInBase){
                    if($file == $fileInBase['nomFichierMedia']){
                        $isInBase = true;
                    }
                }

                if(!$isInBase){
                    unlink("C:\\xampp\\htdocs\\M152\\assets\\medias\\".$file);
                    echo "Fichier supprimé : ".$file;
                    echo "<br>";
                }
            }
        }

        foreach($result as $fileInBase){
            if(!file_exists("C:\\xampp\\htdocs\\M152\\assets\\medias\\".$fileInBase['nomFichierMedia'])){
                $req = PDOBlogCfpt::getInstance()->prepare("DELETE FROM media WHERE nomFichierMedia = :nomFichierMedia");
                $req->bindParam(":nomFichierMedia", $fileInBase['nomFichierMedia']);
                $req->execute();
                echo "Fichier supprimé de la base : ".$fileInBase['nomFichierMedia'];
                echo "<br>";
            }
        }
        
        
    }
}
