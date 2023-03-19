<?php
$action = filter_input(INPUT_GET, 'action');

define('MAX_FILE_SIZE', 3);
define('TOTAL_MAX_SIZE', 70);

switch ($action) {
    // affiche la page de post
    case 'show':
        include 'vues/post_form.php';
        break;

    // traite les données du formulaie (validation formulaire)
    case 'validate':
        // récupéraion de la description
        $descriptionPost = filter_input(INPUT_POST, 'descriptionPost', FILTER_SANITIZE_SPECIAL_CHARS);
        // récupération des fichiers
        $fichiersArray = $_FILES["filesPost"];

        createPost($descriptionPost, $fichiersArray);
        break;


    // supprime un post
    case 'delete':
        // récupération du post
        $idPost = filter_input(INPUT_GET, 'idPost', FILTER_SANITIZE_NUMBER_INT);

        // suppression des images
        $medias = Media::getAllMediasByPostId($idPost);

        // debut de la transaction
        PDOBlogCfpt::getInstance()->beginTransaction();

        // suppression de tous les fichiers
        foreach ($medias as $media) {
            if (unlink("./assets/medias/" . $media->getNomFichierMedia())) {
                Media::DeleteMedia($media->getIdMedia());
            } else {
                // on cancel si un fichier n'a pas pu être supprimé
                PDOBlogCfpt::getInstance()->rollBack();
                // retourne un message d'erreur
                $_SESSION['message'] = [
                    'type' => "danger",
                    'content' => "Un fichier n'a pas pu être supprimé. Merci de ressayer."
                ];
                header('Location: index.php');
            }
        }
        Post::DeletePost($idPost);
        PDOBlogCfpt::getInstance()->commit();
        $_SESSION['message'] = [
            'type' => "success",
            'content' => "Le post a bien été supprimé."
        ];
        header('Location: index.php');
        break;

    // affiche le formulaire de modification d'un post
    case 'edit':
        // récupération du post
        $idPost = filter_input(INPUT_GET, 'idPost', FILTER_SANITIZE_NUMBER_INT);
        $post = Post::GetPostById($idPost);
        $_SESSION['idEditPost'] = $idPost;
        include 'vues/editPost_form.php';
        break;

    // valide le formulaire de modification de post
    case 'validateEdit':
        // récupéraion de la description
        $descriptionPost = filter_input(INPUT_POST, 'descriptionPost', FILTER_SANITIZE_STRING);
        // récupération des fichiers
        $fichiersArray = $_FILES["filesPost"];

        createPost($descriptionPost, $fichiersArray);
        break;

    // supprime un media dans le formulaire de modification de post
    case 'deleteMedia':
        $idMedia = filter_input(INPUT_GET, 'idMedia', FILTER_SANITIZE_NUMBER_INT);
        $nomFichier = Media::GetMediaNameById($idMedia);


        if (unlink("./assets/medias/" . $nomFichier)) {
            Media::DeleteMedia($idMedia);
            $_SESSION['message'] = [
                'type' => "success",
                'content' => "Le média No. " . $idMedia . " a bien été supprimé"
            ];
        } else {
            // on cancel si un fichier n'a pas pu être supprimé
            PDOBlogCfpt::getInstance()->rollBack();
            // retourne un message d'erreur
            $_SESSION['message'] = [
                'type' => "danger",
                'content' => "Un fichier n'a pas pu être supprimé. Merci de ressayer."
            ];
        }
        header('Location: index.php?uc=post&action=edit&idPost=' . $_SESSION['idEditPost']);
        break;

    case 'UploadWithAjax':
        $fichiersArray = $_FILES["file"];
        $descriptionPost = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        include '../models/Media.php';
        include '../models/Post.php';
        include '../models/PDOBlogCfpt.php';

        createPost($descriptionPost, $fichiersArray);
        break;

    default:
        include 'vues/erreur404.php'; // affiche la page d'erreur 404 si le lien n'est pas valide
        break;
}

function createPost($descriptionPost, $fichiersArray)
{
    // verification si les champs ont été remplis
    if ($descriptionPost != "") {


        $totalMo = 0;

        if ($fichiersArray['name'][0] != "") {
            // récupérer les fichiers
            $newImagesArray = [];
            for ($i = 0; $i < count($fichiersArray['name']); $i++) {

                // vérifier si le fichier est une image
                if (explode("/", $fichiersArray['type'][$i])[0] != "image" && explode("/", $fichiersArray['type'][$i])[0] != "video" && explode("/", $fichiersArray['type'][$i])[0] != "audio") {
                    $_SESSION['message'] = [
                        'type' => "danger",
                        'content' => "Les fichiers ne peuvent être que des images, vidéos ou audio !"
                    ];
                    header('Location: index.php?uc=post&action=edit&idPost=' . $_SESSION['idEditPost']);
                }


                $fileMo = Media::ConvertOctetsToMO($fichiersArray['size'][$i]);
                // vérifie la taille de chaque image afin de ne pas dépacer 3 Mo
                if ($fileMo > 3) {
                    $_SESSION['message'] = [
                        'type' => "danger",
                        'content' => "Chaque image doit faire moins de 3 Mo !"
                    ];
                    header('Location: index.php?uc=post&action=edit&idPost=' . $_SESSION['idEditPost']);
                } else {
                    $totalMo .= $fileMo;
                }

                // vérification de la taille totale de tous les fichiers afin de ne pas dépacer 70 Mo
                if ($totalMo > 70) {
                    $_SESSION['message'] = [
                        'type' => "danger",
                        'content' => "Le total des fichiers doit faire moins de 70 Mo !"
                    ];
                    header('Location: index.php?uc=post&action=edit&idPost=' . $_SESSION['idEditPost']);
                }
                $tkt = $fichiersArray['tmp_name'][$i];
                $newImagesArray[$i] = [
                    "name" => $fichiersArray['name'][$i],
                    "type" => $fichiersArray['type'][$i],
                    "tmp_name" => $fichiersArray['tmp_name'][$i],
                    "size" => $fichiersArray['size'][$i]
                ];
            }
        }



        if (intval(Media::CountAllMediasPerPost($_SESSION['idEditPost'])) > 0 || $fichiersArray['name'][0] != "") {
            $currentDate = date("Y/m/d/H/i/s");

            // Début de la transaction
            PDOBlogCfpt::getInstance()->beginTransaction();
            try {
                // on crée le post dans la base de données
                $post = new Post();
                $post->setCommentairePost($descriptionPost)
                    ->setModificationDatePost($currentDate);
                Post::UpdatePost($post);
                $idPost = $_SESSION['idEditPost'];

                // on crée les médias dans la base de données
                $dirFile = "../assets/medias/";
                foreach ($newImagesArray as $imageArray) {
                    $randomName = Media::GenerateRandomImageName() . "." . explode("/", $imageArray['type'])[1];

                    while (file_exists($dirFile . $randomName)) {
                        $randomName = Media::GenerateRandomImageName() . "." . explode("/", $imageArray['type'])[1];
                    }

                    $filepath = $dirFile . $randomName;

                    list($w, $h) = getimagesize($imageArray["tmp_name"]);

                    $newWidth = 500;
                    $newHeight = 300;

                    $newImage = null;

                    if ($imageArray["type"] == IMAGETYPE_JPEG) {
                        $newImage = imagecreatefromjpeg($imageArray["tmp_name"]);
                    } else if ($imageArray["type"] == IMAGETYPE_PNG) {
                        $newImage = imagecreatefrompng($imageArray["tmp_name"]);
                    }

                    $newImage = resize_image($newWidth, $newHeight, $newImage, $w, $h);

                    if (save_image($newImage, $filepath, $imageArray["type"])) {
                        $media = new Media();
                        $media->setTypeMedia($imageArray['type'])
                            ->setNomFichierMedia($randomName)
                            ->setCreationDate($currentDate)
                            ->setModificationDate($currentDate)
                            ->setIdPost($idPost);
                        Media::AddMedia($media);
                    } else {
                        // si il y a un fichier qui ne se push pas rollback et cancel les requêtes
                        PDOBlogCfpt::getInstance()->rollBack();
                        $_SESSION['message'] = [
                            'type' => "danger",
                            'content' => "Une image n'a pas pu être publié !" . $tkt
                        ];
                        header('Location: index.php?uc=post&action=edit&idPost=' . $_SESSION['idEditPost']);
                    }
                }
            } catch (Exception $e) {
                PDOBlogCfpt::getInstance()->rollBack();
                $_SESSION['message'] = [
                    'type' => "danger",
                    'content' => "Une image n'a pas pu être publié !"
                ];
                header('Location: index.php?uc=post&action=edit&idPost=' . $_SESSION['idEditPost']);
            }
        } else {
            $_SESSION['message'] = [
                'type' => "danger",
                'content' => "Merci de choisir au moins une image pour le post !"
            ];
            header('Location: index.php?uc=post&action=edit&idPost=' . $_SESSION['idEditPost']);
        }

        // on push les infos dans base de donnée avec le commit
        PDOBlogCfpt::getInstance()->commit();

        // message de success de création du post et des médias
        $_SESSION['message'] = [
            'type' => "success",
            'content' => "Le post à bien été modifié et tous les fichiers ont été importés"
        ];
        header('Location: index.php');
    } else {
        // retourne un message d'erreur si les champs ne sonts pas remplis
        $_SESSION['message'] = [
            'type' => "danger",
            'content' => "Merci de remplir tous les champs !"
        ];
        header('Location: index.php?uc=post&action=edit&idPost=' . $_SESSION['idEditPost']);
    }


    $_SESSION['idEditPost'] = null;
}

function resize_image($new_width, $new_height, $image, $width, $height)
{
    $new_image = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    return $new_image;
}

function save_image($new_image, $new_filename, $new_type = 'jpeg', $quality = 80)
{
    try {
        if ($new_type == 'jpeg') {
            imagejpeg($new_image, $new_filename, $quality);
        } elseif ($new_type == 'png') {
            imagepng($new_image, $new_filename);
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}