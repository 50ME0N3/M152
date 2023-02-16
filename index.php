<?php
session_start();

if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = [
        'type' => null,
        'content' => null
    ];
    $_SESSION['idEditPost'] = null;
}
ini_set('display_errors', 1);
$uc = filter_input(INPUT_GET, 'uc') == null ? "home" : filter_input(INPUT_GET, 'uc'); // affiche la page accueil par défaut

include 'models/PDOBlogCfpt.php';
include 'models/Post.php';
include 'models/Media.php';

// afichage du header
if($uc != "getAllPosts"){
include 'vues/header.php';
}

// Gestion des affichages
switch ($uc) {
        // Affichage de la page d'accueil
    case 'home':
        $posts = Post::getAllPosts();
        include "vues/home.php"; // affiche la vue d'accueil
        break;
        // redirection sur le controller post
    case 'post':
        include 'controllers/post_controller.php';
        break;

}

// Affichage du footer
if($uc != "getAllPosts"){
include 'vues/footer.php';
}
error_reporting(E_ALL);
