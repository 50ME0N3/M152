	<div class="padding">

		<div class="full col-sm-9">
			<!-- content -->
			<div class="row">


				<?php
				// message d'erreur
				if ($_SESSION['message']['type'] != null) { ?>
					<div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<?= $_SESSION['message']['content'] ?>
					</div>
				<?php
					$_SESSION['message'] = [
						'type' => null,
						'content' => null
					];
				}
				?>

				<!-- main col left -->
				<div class="col-sm-6 background">

					<div class="panel panel-default" style="max-width: 976px;">
						<div class="panel-thumbnail animate__animated animate__zoomIn" style="width: 100%;"><img src="assets/img/tree.jpg" class="img-responsive"></div>
						<div class="panel-body">
							<p class="lead animate__animated animate__pulse">Le blog d'Antoine Davet</p>

						</div>
					</div>
				</div>

				<!-- main col right -->
				<div class="col-sm-6 background">
					<div class="panel panel-default animate__animated animate__zoomIn" style="width: 100%;">
						<div class="panel-heading">
							<h4>Message de bienvenue</h4>
						</div>
						<div class="panel-body">
							<h2>Bienvenue sur mon blog</h2>
							<div class="clearfix">
								<img src="./assets/img/earth.jpg" alt="vagabon" width="100%">
							</div>

						</div>
					</div>

					<?php
					foreach ($posts as $post) {
						$medias = Media::getAllMediasByPostId($post->getIdPost());
					?>

						<div class="panel panel-default animate__animated animate__zoomIn">
							<div class="panel-heading">
								<div class="row">

									<!-- main col left -->
									<div class="col-sm-6">
										<p>Posté le <?= date_format(date_create($post->getCreationDatePost()), 'd m Y, H:i:s'); ?>
											<br>
											Modifié le <?= date_format(date_create($post->getModificationDatePost()), 'd m Y, H:i:s'); ?>
										</p>
									</div>
									<div class="col-sm-6" style="text-align: right;">
										<a class="btn btn-primary" href="index.php?uc=post&action=edit&idPost=<?= $post->getIdPost() ?>">Modifier</a>
										<a class="btn btn-danger" href="index.php?uc=post&action=delete&idPost=<?= $post->getIdPost() ?>">X</a>
									</div>
								</div>
							</div>

							<div class="panel-body">

								<!-- Carousel container -->
								<div id="carousel<?= $post->getIdPost(); ?>" class="carousel slide" data-ride="carousel">

									<!-- Content -->
									<div class="carousel-inner" role="listbox">

										<?php
										$count = 0;
										foreach ($medias as $media) {
											// Si le media est une image
											switch (explode("/", $media->getTypeMedia())[0]) {
												case 'image':
										?>
													<!-- Slide -->
													<div class="item <?= $count == 0 ? "active" : "" ?>">
														<img src="./assets/medias/<?= $media->getNomFichierMedia() ?>" alt="Sunset over beach" width="100%">
													</div>
												<?php
													break;
												case 'video':
												?>
													<div class="item <?= $count == 0 ? "active" : "" ?>">
														<!-- Pour que l'attribut autoplay marche, il faut l'attribut muted -->
														<video controls autoplay loop muted width="100%">
															<source src="./assets/medias/<?= $media->getNomFichierMedia() ?>" type="<?= $media->getTypeMedia() ?>">
														</video>
													</div>
												<?php
													break;
												case 'audio':
												?>
													<div class="item <?= $count == 0 ? "active" : "" ?>">
														<audio controls src="./assets/medias/<?= $media->getNomFichierMedia() ?>" style="width: 50%; margin-left: 20%"></audio>
													</div>
										<?php

													break;
											}
											$count++;
										}
										?>


									</div>

									<?php
									if ($count > 1) {
									?>
										<!-- Previous/Next controls -->
										<a class="left carousel-control" href="#carousel<?= $post->getIdPost(); ?>" role="button" data-slide="prev">
											<span class="icon-prev" aria-hidden="true"></span>
											<span class="sr-only">Previous</span>
										</a>
										<a class="right carousel-control" href="#carousel<?= $post->getIdPost(); ?>" role="button" data-slide="next">
											<span class="icon-next" aria-hidden="true"></span>
											<span class="sr-only">Next</span>
										</a>
									<?php
									}
									?>

								</div>



								<br>
								<p class="lead"><?= $post->getCommentairePost(); ?></p>
							</div>
						</div>

					<?php
					}
					?>
				</div>
			</div>
			<!--/row-->
			<hr>
			<h4 class="text-center">
				<a href="http://usebootstrap.com/theme/facebook" target="ext">Copyright © 2023</a>
			</h4>
			<hr>
		</div><!-- /col-9 -->
	</div><!-- /padding -->