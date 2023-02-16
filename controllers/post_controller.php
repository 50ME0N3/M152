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
   
        // verification si les champs ont été remplis
        if ($descriptionPost != "" && $fichiersArray['name'][0] != "") {

            $totalMo = 0;

            // récupérer les fichiers
            $newImagesArray = [];
            for ($i = 0; $i < count($fichiersArray['name']); $i++) {


                // vérifier si le fichier est une image
                if (explode("/", $fichiersArray['type'][$i])[0] != "image" && explode("/", $fichiersArray['type'][$i])[0] != "video" && explode("/", $fichiersArray['type'][$i])[0] != "audio") {
                    $_SESSION['message'] = [
                        'type' => "danger",
                        'content' => "Les fichiers ne peuvent être que des images, vidéos ou audio !"
                    ];
                    header('Location: index.php?uc=post&action=show');
                }


                $fileMo = Media::ConvertOctetsToMO($fichiersArray['size'][$i]);
                // vérifie la taille de chaque image afin de ne pas dépacer 3 Mo
                if ($fileMo > MAX_FILE_SIZE) {
                    $_SESSION['message'] = [
                        'type' => "danger",
                        'content' => "Chaque image doit faire moins de 3 Mo !"
                    ];
                    header('Location: index.php?uc=post&action=show');
                } else {
                    $totalMo .= $fileMo;
                }

                // vérification de la taille totale de tous les fichiers afin de ne pas dépacer 70 Mo
                if ($totalMo > TOTAL_MAX_SIZE) {
                    $_SESSION['message'] = [
                        'type' => "danger",
                        'content' => "Le total des fichiers doit faire moins de 70 Mo !"
                    ];
                    header('Location: index.php?uc=post&action=show');
                }

                $newImagesArray[$i] = [
                    "name" => $fichiersArray['name'][$i],
                    "type" => $fichiersArray['type'][$i],
                    "tmp_name" => $fichiersArray['tmp_name'][$i],
                    "size" => $fichiersArray['size'][$i]
                ];
            }

            $currentDate = date("Y/m/d/H/i/s");

            // Début de la transaction
            PDOBlogCfpt::getInstance()->beginTransaction();

            // on crée le post dans la base de données
            $post = new Post(); 
            $post->setCommentairePost($descriptionPost)
                ->setCreationDatePost($currentDate)
                ->setModificationDatePost($currentDate);
            $idPost = Post::AddPost($post);

            // on crée les médias dans la base de données
            $dirFile = "./assets/medias/";
            try {
                foreach ($newImagesArray as $imageArray) {
                    $randomName = Media::GenerateRandomImageName($dirFile) . "." . explode("/", $imageArray['type'])[1];

                    while (file_exists($dirFile . $randomName)) {
                        $randomName = Media::GenerateRandomImageName() . "." . explode("/", $imageArray['type'])[1];
                    }

                    $filepath = $dirFile . $randomName;

                    if (move_uploaded_file($imageArray['tmp_name'], $filepath)) {
                        $media = new Media();
                        $media->setTypeMedia($imageArray['type'])
                            ->setNomFichierMedia($randomName)
                            ->setCreationDate($currentDate)
                            ->setModificationDate($currentDate)
                            ->setIdPost($idPost);
                        Media::AddMedia($media);
                    } else {
                        PDOBlogCfpt::getInstance()->rollBack();
                        $_SESSION['message'] = [
                            'type' => "danger",
                            'content' => "Une image n'a pas pu être publié !"
                        ];
                        header('Location: index.php?uc=post&action=show');
                    }
                }
            } catch (Exception $e) {

                PDOBlogCfpt::getInstance()->rollBack();
                $_SESSION['message'] = [
                    'type' => "danger",
                    'content' => "Une image n'a pas pu être publié !"
                ];
                header('Location: index.php?uc=post&action=show');
            }

            // on push les infos dans base de donnée avec le commit
            PDOBlogCfpt::getInstance()->commit();

            // message de success de création du post et des médias
            $_SESSION['message'] = [
                'type' => "success",
                'content' => "Votre post a bien été publié !"
            ];
            header('Location: index.php?uc=post&action=show');
        } else {
            // retourne un message d'erreur si les champs ne sonts pas remplis
            $_SESSION['message'] = [
                'type' => "danger",
                'content' => "Veuillez remplir tous les champs !"
            ];
            //header('Location: index.php?uc=post&action=show');
        }
        break;
}
