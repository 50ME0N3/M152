<?php
class Post
{
    private $idPost;
    private $commentairePost;
    private $creationDatePost;
    private $modificationDatePost;
    private $compteurPost;


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
     * Get the value of commentairePost
     */
    public function getCommentairePost()
    {
        return $this->commentairePost;
    }

    /**
     * Set the value of commentairePost
     *
     * @return  self
     */
    public function setCommentairePost($commentairePost)
    {
        $this->commentairePost = $commentairePost;

        return $this;
    }

    /**
     * Get the value of creationDate
     */
    public function getCreationDatePost()
    {
        return $this->creationDatePost;
    }

    /**
     * Set the value of creationDate
     *
     * @return  self
     */
    public function setCreationDatePost($creationDatePost)
    {
        $this->creationDatePost = $creationDatePost;

        return $this;
    }

    /**
     * Get the value of modificationDate
     */
    public function getModificationDatePost()
    {
        return $this->modificationDatePost;
    }

    /**
     * Set the value of modificationDate
     *
     * @return  self
     */
    public function setModificationDatePost($modificationDatePost)
    {
        $this->modificationDatePost = $modificationDatePost;

        return $this;
    }


    /**
     * Get the value of compteurPost
     */
    public function getCompteurPost()
    {
        return $this->compteurPost;
    }

    /**
     * Set the value of compteurPost
     *
     * @return  self
     */
    public function setCompteurPost($compteurPost)
    {
        $this->compteurPost = $compteurPost;
        return $this;
    }

    // *********** Functions ************


    // exemple recuperer les posts
    public static function getAllPosts()
    {
        $req = PDOBlogCfpt::getInstance()->prepare("SELECT * FROM post ORDER BY creationDatePost DESC;");
        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Post'); // methode de fetch
        $req->execute(); // executer la requette

        $result = $req->fetchAll();
        return $result;
    }

    // Supprime un post en fonction de l'id du post
    public static function DeletePost($idPost)
    {
        $req = PDOBlogCfpt::getInstance()->prepare("DELETE FROM post WHERE idPost = :idPost");
        $req->bindParam(":idPost", $idPost);
        $req->execute();
    }


    // ajoute un post dans la base de donnée
    public static function AddPost(Post $post)
    {
        $commentaire = $post->getCommentairePost();
        $creationDate = $post->getCreationDatePost();
        $modificationDate = $post->getModificationDatePost();

        $req = PDOBlogCfpt::getInstance()->prepare("INSERT INTO post(commentairePost, creationDatePost, modificationDatePost) VALUES(:commentaire, :creationDate, :modificationDate);");
        $req->bindParam(":commentaire", $commentaire);
        $req->bindParam(":creationDate", $creationDate);
        $req->bindParam(":modificationDate", $modificationDate);
        $req->execute(); // executer la requette

        return PDOBlogCfpt::getInstance()->lastInsertId();
    }

    public static function GetPostById($idPost)
    {
        $req = PDOBlogCfpt::getInstance()->prepare("SELECT * FROM post WHERE idPost = :idPost;");
        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Post'); // methode de fetch
        $req->bindParam(":idPost", $idPost);
        $req->execute(); // executer la requette

        $result = $req->fetch();
        return $result;
    }

    public static function UpdatePost(Post $post)
    {
        $commentaire = $post->getCommentairePost();
        $date = $post->getModificationDatePost();
        $req = PDOBlogCfpt::getInstance()->prepare("UPDATE post SET commentairePost = :commentaire, modificationDatePost = :modificationDatePost");
        $req->bindParam(":commentaire", $commentaire);
        $req->bindParam(":modificationDatePost", $date);
        $req->execute(); // executer la requette

    }
}
