<?php
/**
 * Education Card Template
 */
?>

<article <?php post_class('education-card h-100'); ?>>
	<a href="<?php the_permalink(); ?>"
	   class="education-card__link d-block position-relative overflow-hidden rounded-4">

		<!-- Image -->
		<div class="education-card__image ratio ratio-1x1">
			<?php if (has_post_thumbnail()) : ?>
				<?php the_post_thumbnail(
					'medium_large',
					['class' => 'w-100 h-100 object-fit-cover']
				); ?>
			<?php endif; ?>
		</div>

		<!-- Overlay -->
		<div class="education-card__overlay
                    position-absolute bottom-0 start-0 end-0
                    d-flex align-items-end p-3">
			<h3 class="education-card__title mb-0 text-white">
				<?php the_title(); ?>
			</h3>
		</div>

	</a>
</article>

