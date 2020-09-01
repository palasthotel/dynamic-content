<?php
/**
 * @var WP_Post $post
 * @var string $slug
 */
?>
<a href="<?php get_permalink($post->ID); ?>" data-dynamic-content-slug="<?php echo $slug; ?>"><?php echo get_the_title($post->ID); ?></a>
