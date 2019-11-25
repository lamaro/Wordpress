//REPEATER CON OBJETO

<?php if( have_rows('cursos') ): ?>
    <?php while ( have_rows('cursos') ) : the_row(); ?>
        <?php $curso = get_sub_field('curso');?>
        <?php if( $curso ): $post = $curso; setup_postdata( $post ); ?>
            <?php the_field('whatever')?>
        <?php wp_reset_postdata(); // IMPORTANTE ?>
        <?php endif; ?>
    <?php endwhile; ?>
<?php endif; ?>


//REPEATER
<?php if( have_rows('cursos_online') ): ?>
    <?php while ( have_rows('cursos_online') ) : the_row(); ?>
        <?php the_sub_field('whaterver')?>
    <?php endwhile; ?>
<?php endif; ?>

//REPEATER de otro post/page nro 45 ej.
<?php if( have_rows('cursos_online',45) ): ?>
    <?php while ( have_rows('cursos_online',45) ) : the_row(); ?>
        <?php the_sub_field('whaterver')?>
    <?php endwhile; ?>
<?php endif; ?>

// WP QUERY POST TYPE
<?php

$query = new WP_Query( array(
	'post_type' => 'podcast'
));

?>

<?php if ( $query->have_posts() ) :?>
<?php while ( $query->have_posts() ) : $query->the_post(); ?>
    <?php echo $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'large'); ?>
<?php endwhile; ?>
<?php wp_reset_postdata(); ?>
<?php endif; ?>

//REPEATER DENTRO DE UN FLEX LAYOUT

<?php if( get_sub_field('item') ): ?>
    <ul>
        <?php while ( have_rows('item') ) : the_row(); ?>
            <li>
                <h4><?php the_sub_field('item_title');?></h4>  
                <p><strong>Medium </strong>$8.50 | <strong>Large </strong>$11.50</p>
            </li>
        <?php endwhile; ?>  
    </ul>
<?php endif; ?>


//FLEX LAYOUT CON REPEATER
<?php if( have_rows('column_1') ):?>
    <?php  while ( have_rows('column_1') ) : the_row(); ?>
        <?php  if( get_row_layout() == 'title' ):  //title?>
            <?php include('food-menu-parts/title.php');?>
        <?php  elseif( get_row_layout() == 'specials' ): //specials?>
            <?php if( get_sub_field('items_layout_1_item') ): ?>
                <h4><?php the_sub_field('items_layout_1_title');?></h4>
                <ul class="patties_list">
                    <?php while ( have_rows('items_layout_1_item') ) : the_row(); ?>
                        <li><p><strong><?php the_sub_field('items_layout_1_item_name');?> | </strong><?php the_sub_field('items_layout_1_item_price');?></p></li>
                    <?php endwhile; ?>  
                </ul>
            <?php endif; ?>
        <?php  endif; ?>
    <?php  endwhile; ?>
<?php // else : ?>
<?php endif; //Columna 1?>

//FLEX LAYOUT SIN REPEATER


<?php if( have_rows('column_1') ):?>
    <?php  while ( have_rows('column_1') ) : the_row(); ?>
        <?php  if( get_row_layout() == 'slide_with_background' ):  //title?>
            <?php include('food-menu-parts/title.php');?>
        <?php  elseif( get_row_layout() == 'youtube_video' ): //specials?>
            <?php include('food-menu-parts/specials.php');?>
        <?php  elseif( get_row_layout() == 'youtube_video_titulo' ): //specials?>
            <?php include('food-menu-parts/specials.php');?>
        <?php  endif; ?>
    <?php  endwhile; ?>
<?php // else : ?>
<?php endif; //Columna 1?>
