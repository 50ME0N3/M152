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
							</div>
							</h4>
						</div>

						<div class="panel-body">
							<?php
							foreach ($medias as $media) {
								// Si le media est une image
								switch (explode("/", $media->getTypeMedia())[0]) {
									case 'image':
							?>
										<!-- Slide -->
										<div class="item">
											<img src="./assets/medias/<?= $media->getNomFichierMedia() ?>" alt="Sunset over beach" width="100%">

										</div>
							<?php
										break;
								}
							}
							?>
							<br>
							<p class="lead"><?= $post->getCommentairePost(); ?></p>
							<button>Modifier</button>
							<button>Supprimer</button>
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
			<a href="http://usebootstrap.com/theme/facebook" target="ext">Copyright © 2021</a>
		</h4>
		<hr>
	</div><!-- /col-9 -->
</div><!-- /padding -->